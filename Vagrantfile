# -*- mode: ruby -*-
# vi: set ft=ruby :

BOX          = "jessie64_symfony"
BOX_URL      = "http://vagrant.ideaplexus.com/boxes/jessie64_symfony.box"
BOX_CHECKSUM = "ef39e72810ce5ef42a6d863258de9e6de3addb1c9a2bfe0489258f3bcaeca606"
BOX_MEMORY   = 1024

# nfs configuration
BOX_NFS_EXPORT  = true
BOX_NFS_UDP     = false
BOX_NFS_VERSION = 3

Vagrant.configure(2) do |config|

  # define box
  config.vm.box = BOX
  config.vm.box_url = BOX_URL
  config.vm.box_download_checksum = BOX_CHECKSUM
  config.vm.box_download_checksum_type = "sha256"
  config.vm.box_check_update = true

  # define access
  config.ssh.username   = "vagrant"
  config.ssh.password   = 'vagrant'
  config.ssh.insert_key = false

  # allow ssh forwarding to local ssh - currently not needed
  # config.ssh.forward_agent = true

  # mount directories
  config.vm.synced_folder ".", "/var/www",
    type: "nfs",
    nfs_export: BOX_NFS_EXPORT,
    nfs_udp: BOX_NFS_UDP,
    nfs_version: BOX_NFS_VERSION

  # define networks
  config.vm.network "private_network", type: "dhcp"

  # modify box configuration
  config.vm.provider "virtualbox" do |vb|
    vb.customize ["modifyvm", :id, "--memory", BOX_MEMORY]
  end

  # provisioning
  config.vm.provision :shell, inline: "su -c \"
    cd /var/www;
    ant composer
  \" -s /bin/bash vagrant"

end

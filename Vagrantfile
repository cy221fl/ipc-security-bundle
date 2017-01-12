# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

# box configuration
BOX                   = "ideaplexus/jessie64_devbox"
BOX_MEMORY            = 2048
BOX_CHECK_UPDATE      = false
BOX_GUI               = false

# sync configuration
BOX_SYNC_OWNER        = "vagrant"
BOX_SYNC_GROUP        = "vagrant"
BOX_SYNC_MOUNTOPTIONS = [ "dmode=775, fmode=775" ]
BOX_SYNC_SOURCE       = "."
BOX_SYNC_TARGET       = "/var/www"
BOX_SYNC_METHOD       = "nfs"

# nfs configuration
BOX_NFS_EXPORT        = true
BOX_NFS_UDP           = true
BOX_NFS_VERSION       = 3

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  # define box
  config.vm.box                        = BOX
  config.vm.box_check_update           = BOX_CHECK_UPDATE

  # forward ssh
  config.ssh.forward_agent = true

  # define network
  config.vm.network "private_network", type: "dhcp"

  # modify box configuration
  config.vm.provider "virtualbox" do |vb|
    vb.gui = BOX_GUI
    vb.customize ["modifyvm", :id, "--memory", BOX_MEMORY]
    vb.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
  end

  # mount directory
  sync_options = { }
  case BOX_SYNC_METHOD
  when "nfs"
    sync_options[:type]        = BOX_SYNC_METHOD
    sync_options[:nfs_export]  = BOX_NFS_EXPORT
    sync_options[:nfs_udp]     = BOX_NFS_UDP,
    sync_options[:nfs_version] = BOX_NFS_VERSION
    if Vagrant::Util::Platform.windows?
      sync_options[:owner] = BOX_SYNC_OWNER
      sync_options[:group] = BOX_SYNC_GROUP
    else
      config.nfs.map_uid = Process.uid
      config.nfs.map_gid = Process.gid
    end
  else
    sync_options[:owner]         = BOX_SYNC_OWNER
    sync_options[:group]         = BOX_SYNC_GROUP
    sync_options[:mount_options] = BOX_SYNC_MOUNTOPTIONS
  end
  config.vm.synced_folder BOX_SYNC_SOURCE, BOX_SYNC_TARGET, sync_options

  # provisioning
  config.vm.provision :shell, inline: "su -c \"
    echo 'cd /var/www' >> ~/.bashrc;
    cd /var/www;
    ant composer;
  \" -s /bin/bash vagrant"

end

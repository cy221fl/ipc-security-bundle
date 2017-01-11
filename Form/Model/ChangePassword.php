<?php

namespace IPC\SecurityBundle\Form\Model;

class ChangePassword implements ChangePasswordInterface
{

    /**
     * Current password
     *
     * @var string
     */
    protected $current;

    /**
     * New password
     *
     * @var string
     */
    protected $new;

    /**
     * Repeated password
     *
     * @var string
     */
    protected $repeated;

    /**
     * @return string
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * @param string $current
     * @return $this
     */
    public function setCurrent($current)
    {
        $this->current = $current;
        return $this;
    }

    /**
     * @return string
     */
    public function getNew()
    {
        return $this->new;
    }

    /**
     * @param string $new
     * @return $this
     */
    public function setNew($new)
    {
        $this->new = $new;
        return $this;
    }

    /**
     * @return string
     */
    public function getRepeated()
    {
        return $this->repeated;
    }

    /**
     * @param string $repeated
     * @return $this
     */
    public function setRepeated($repeated)
    {
        $this->repeated = $repeated;
        return $this;
    }
}
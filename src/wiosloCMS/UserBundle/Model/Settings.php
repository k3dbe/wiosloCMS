<?php

namespace wiosloCMS\UserBundle\Model;

use wiosloCMS\UserBundle\Model\om\BaseSettings;

class Settings extends BaseSettings
{
    private $localSettings = array();

    public function get($name, $default = null)
    {
        if ($this->has($name)) {
            return $this->localSettings[$name];
        }

        if (null !== $default) {
            return $default;
        }

        $message = sprintf('Non existent %s setting.', $name);
        throw new \InvalidArgumentException($message);
    }

    public function has($name)
    {
        if (is_array($this->localSettings) && array_key_exists($name, $this->localSettings)) {
            return true;
        }

        return false;
    }

    public function add($name, $setting)
    {
        if (!$this->has($name)) {
            return $this->set($name, $setting);
        }

        $message = sprintf('Setting %s already exists. Cannot overwrite.', $setting);
        throw new \InvalidArgumentException($message);
    }

    public function set($name, $setting)
    {
        $this->localSettings[$name] = $setting;

        return $this;
    }

    public function remove($name)
    {
        if ($this->has($name)) {
            unset($this->localSettings[$name]);
        }

        return $this;
    }

    public function flush()
    {
        $this->localSettings = array();
    }

    public function hydrate($row, $startcol = 0, $rehydrate = false)
    {
        $startcol = parent::hydrate($row, $startcol, $rehydrate);

        $this->localSettings = json_decode(parent::getSettings(), true);

        return $startcol;
    }

    public function preSave(\PropelPDO $con = null)
    {
        $this->setSettings(json_encode($this->localSettings));

        return true;
    }
}

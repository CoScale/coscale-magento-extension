<?php

class CoScale_Monitor_Model_Output_Generator extends Varien_Object
{
    public function __construct()
    {
        $logger = Mage::helper('coscale_monitor');
        Mage::dispatchEvent('coscale_output_generator', array('output' => $this, 'logger'=>$logger));
    }

    public function addMetric($data)
    {
        return $this->addArray('metrics', $data);
    }

    public function addEvent($data)
    {
        return $this->addArray('events', $data);
    }

    public function addCustom($name, $data)
    {
        return $this->addArray($name, $data);
    }

    protected function addArray($name, $data)
    {
        $origData = $this->getData($name);
        $origData[] = $data;
        $this->setData($name, $origData);
        return $this;
    }

    public function getJsonOutput()
    {
        return $this->toJson();
    }
}

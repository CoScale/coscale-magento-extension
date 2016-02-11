<?php

class CoScale_Monitor_Model_Metric_System
{
    public function generate(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();

        /** @var CoScale_Monitor_Helper_Data $logger */
        $logger = $event->getLogger();
        /** @var CoScale_Monitor_Model_Output_Generator $output */
        $output = $event->getOutput();

        // Get Installed Modules
        try {
            $logger->debugStart('Installed Modules');

            $output->addCustom('modules', array('name' => 'core', 'version' => (string)Mage::getVersion()));
            foreach (Mage::getConfig()->getNode('modules')->children() as $module) {
                $output->addCustom('modules', array('name' => $module->getName(), 'version' => (string)$module->version));
            }
            $logger->debugEnd('Installed Modules');
        } catch (Exception $ex) {
            $logger->debugEndError('Installed Modules', $ex);
        }

        // Get Stores collection
        try {
            $logger->debugStart('Stores');

            foreach (Mage::app()->getStores() as $store) {
                $output->addCustom('stores', array('name' => $store->getName(), 'id' => (int)$store->getId()));
            }
            $logger->debugEnd('Stores');
        } catch (Exception $ex) {
            $logger->debugEndError('Stores', $ex);
        }
    }
}
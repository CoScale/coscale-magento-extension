<?php

class CoScale_Monitor_Model_Metric_Observer
{
    public function generate(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();

        /** @var CoScale_Monitor_Helper_Data $logger */
        $logger = $event->getLogger();
        /** @var CoScale_Monitor_Model_Output_Generator $output */
        $output = $event->getOutput();

        // Get Metrics Collection
        try {
            $logger->debugStart('Metrics Collection');

            $metricOrderDelete = Mage::getSingleton('coscale_monitor/metric_order');
            $collection = Mage::getModel('coscale_monitor/metric')->getCollection();
            /** @var CoScale_Monitor_Model_Metric $metric */
            foreach ($collection as $metric) {
                $data = array(
                    'name' => $metric->getName(),
                    'unit' => $metric->getUnit(),
                    'value' => (float)$metric->getValue(),
                    'store_id' => (int)$metric->getStoreId(),
                    'type' => $metric->getTypeText()
                );
                if ($code = $metric->getCalculationTypeCode()) {
                    $data['calctype'] = $code;
                }
                $output->addMetric($data);

                // Check if metric need to be reset after collection
                if ($metricOrderDelete->resetOnCollect($metric->getKey())) {
                    $metric->setValue(0);
                }
            }
            $logger->debugEnd('Metrics Collection');
        } catch (Exception $ex) {
            $logger->debugEndError('Metrics Collection', $ex);
        }
    }
}
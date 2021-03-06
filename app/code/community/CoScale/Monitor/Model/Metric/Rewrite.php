<?php
/**
 * Metrics related to ReWrites
 *
 * @package CoScale_Monitor
 * @author  Vladimir Kerkhoff <v.kerkhoff@genmato.com>
 * @version 1.0
 * @created 2015-08-18
 */
class CoScale_Monitor_Model_Metric_Rewrite extends CoScale_Monitor_Model_Metric_Abstract
{

    public function generate(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();

        /** @var CoScale_Monitor_Helper_Data $logger */
        $logger = $event->getLogger();
        /** @var CoScale_Monitor_Model_Output_Generator $output */
        $output = $event->getOutput();

        // Get URL Rewrites
        try {
            $logger->debugStart('Rewrites');

            foreach ($this->getUrlRewrites() as $data) {
                $output->addMetric($data);
            }

            $logger->debugEnd('Rewrites');
        } catch (Exception $ex) {
            $logger->debugEndError('Rewrites', $ex);
        }
    }

    /**
     * Get amount of rewrites
     * @return array
     */
    public function getUrlRewrites()
    {
        $collection = Mage::getResourceModel('core/url_rewrite_collection');
        if (!is_object($collection)) {
            return array();
        }
        $collection->getSelect()
            ->reset('columns')
            ->columns(array('store_id' => 'main_table.store_id',
                'count' => 'COUNT(*)'))
            ->group('main_table.store_id');

        foreach ($collection as $rewrite) {
            $output[] = array(
                'name' => 'URL Rewrites',
                'unit' => 'rewrites',
                'value' => (float)$rewrite->getCount(),
                'store_id' => (int)$rewrite->getStoreId(),
                'type' => 'A'
            );
        }
        return $output;
    }
}
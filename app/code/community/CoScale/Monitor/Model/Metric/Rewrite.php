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

    /**
     * Get amount of rewrites
     * @return array
     */
    public function getUrlRewrites()
    {
        $collection = Mage::getResourceModel('core/url_rewrite_collection');
        $collection->getSelect()
            ->reset('columns')
            ->columns(array('store_id' => 'main_table.store_id',
                'count' => 'COUNT(*)'))
            ->group('main_table.store_id');

        foreach ($collection as $rewrite) {
            $output[] = array(
                'name' => 'URL Rewrites',
                'unit' => 'rewrites',
                'value' => $rewrite->getCount(),
                'store_id' => $rewrite->getStoreId(),
                'type' => 'A'
            );
        }
        return $output;
    }
}
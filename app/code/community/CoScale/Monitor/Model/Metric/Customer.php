<?php

/**
 * Observer for the metrics related to customers
 *
 * @package CoScale_Monitor
 * @author  Rian Orie <rian.orie@supportdesk.nu>
 * @version 1.0
 * @created 2015-07-03
 */
class CoScale_Monitor_Model_Metric_Customer extends CoScale_Monitor_Model_Metric_Abstract
{
    /**
     * Identifier for the total number of customers in the system
     */
    const KEY_CUSTOMER_TOTAL = 1000;
    /**
     * Identifier for the total number of customers in the system
     */
    const KEY_CUSTOMER_TODAY = 1001;

    /**
     * Public contructor function
     */
    public function _contruct()
    {
        $this->_metricData[self::KEY_CUSTOMER_TOTAL] = array(
            'name' => 'Customers',
            'description' => 'The total number of customers in the system',
            'unit' => 'customers'
        );
    }

    /**
     * Observe the adding of new customers to the system
     *
     * @param Varien_Event_Observer $observer
     */
    public function addNew(Varien_Event_Observer $observer)
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = $observer->getEvent()->getCustomer();

        if ($customer->getOrigData('entity_id') == $customer->getId()) {
            return;
        }

        $this->setMetric(
            self::ACTION_INCREMENT,
            self::KEY_CUSTOMER_TOTAL,
            $customer->getStore()->getId(),
            1
        );
    }

    /**
     * Cronjob to update the total number of customers
     */
    public function dailyCron()
    {
        $this->updateTotalCount();
    }

    /**
     * Daily update full numbers of customers
     */
    public function updateTotalCount()
    {
        $collection = Mage::getResourceModel('customer/customer_collection');
        if (!is_object($collection)) {
            return;
        }
        $collection->getSelect()
            ->reset('columns')
            ->columns(array('website_id' => 'e.website_id',
                'customer_count' => 'COUNT(*)'))
            ->group('e.website_id');

        foreach ($collection as $customer) {
            $storeIds = Mage::app()->getWebsite($customer->getWebsiteId())->getStoreIds();
            foreach ($storeIds as $storeId) {
                $this->setMetric(
                    self::ACTION_UPDATE,
                    self::KEY_CUSTOMER_TOTAL,
                    $storeId,
                    $customer->getCustomerCount()
                );
            }
        }
    }
}
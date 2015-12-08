<?php

/**
 * Observer for the metrics related to orders
 *
 * @package CoScale_Monitor
 * @author  Rian Orie <rian.orie@supportdesk.nu>
 * @version 1.0
 * @created 2015-07-07
 */
class CoScale_Monitor_Model_Metric_Order extends CoScale_Monitor_Model_Metric_Abstract
{

    /**
     * Identifier for total orders
     */
    const KEY_ORDER_TOTAL = 2000;
    const KEY_ORDER_TOTAL_TODAY = 2001;
    const KEY_ORDER_TOTAL_NEW = 2002;

    /**
     * Identifier for order amount average and total
     */
    const KEY_ORDER_AMOUNT_AVERAGE = 2010;
    const KEY_ORDER_SIZE_TOTAL = 2011;
    const KEY_ORDER_AMOUNT_AVERAGE_NEW = 2012;
    const KEY_ORDER_SIZE_TOTAL_NEW = 2013;

    /**
     * Identifier for order size average and total
     */
    const KEY_ORDER_SIZE_AVERAGE = 2020;
    const KEY_ORDER_AMOUNT_TOTAL = 2021;
    const KEY_ORDER_SIZE_AVERAGE_NEW = 2022;
    const KEY_ORDER_AMOUNT_TOTAL_NEW = 2023;

    /**
     * Identifier for order state processing/completed
     */
    const KEY_ORDER_STATE_NEW = 2030;
    const KEY_ORDER_STATE_PROCESSING = 2031;
    const KEY_ORDER_STATE_COMPLETED = 2032;

    /**
     * Public contructor function
     */
    public function _contruct()
    {
        $this->_metricData[self::KEY_ORDER_SIZE_TOTAL_NEW] = array(
            'name' => 'New Order size total',
            'description' => 'The total size of orders since last collect for this store',
            'unit' => 'items'
        );

        $this->_metricData[self::KEY_ORDER_SIZE_AVERAGE_NEW] = array(
            'name' => 'Order size average',
            'description' => 'The average size of orders since last collect for this store',
            'unit' => 'items'
        );

        $this->_metricData[self::KEY_ORDER_AMOUNT_TOTAL_NEW] = array(
            'name' => 'New Order amount total',
            'description' => 'The total amount of orders since last collect for this store',
            'unit' => 'Amount'
        );

        $this->_metricData[self::KEY_ORDER_AMOUNT_AVERAGE_NEW] = array(
            'name' => 'Order amount average',
            'description' => 'The average amount of orders since last collect for this store',
            'unit' => 'Amount'
        );

        $this->_metricData[self::KEY_ORDER_TOTAL_NEW] = array(
            'name' => 'Total New Orders',
            'description' => 'The total number of orders since last collect for this store',
            'unit' => 'orders'
        );

        $this->_metricData[self::KEY_ORDER_SIZE_TOTAL] = array(
            'name' => 'Order size total',
            'description' => 'The total size of all order in the system for this store',
            'unit' => 'items'
        );

        $this->_metricData[self::KEY_ORDER_SIZE_AVERAGE] = array(
            'name' => 'Order size total average',
            'description' => 'The average size of an order in the system for this store',
            'unit' => 'items'
        );

        $this->_metricData[self::KEY_ORDER_AMOUNT_TOTAL] = array(
            'name' => 'Order amount total',
            'description' => 'The total amount of an order in the system for this store',
            'unit' => 'Amount'
        );

        $this->_metricData[self::KEY_ORDER_AMOUNT_AVERAGE] = array(
            'name' => 'Order amount total average',
            'description' => 'The average amount of an order in the system for this store',
            'unit' => 'Amount'
        );

        $this->_metricData[self::KEY_ORDER_TOTAL] = array(
            'name' => 'Orders',
            'description' => 'The total number of orders in the system for this store',
            'unit' => 'orders'
        );

        $this->_metricData[self::KEY_ORDER_STATE_NEW] = array(
            'name' => 'Orders new',
            'description' => 'The total number of orders in new state',
            'unit' => 'orders'
        );

        $this->_metricData[self::KEY_ORDER_STATE_PROCESSING] = array(
            'name' => 'Orders processing',
            'description' => 'The total number of orders in processing state',
            'unit' => 'orders'
        );

        $this->_metricData[self::KEY_ORDER_STATE_COMPLETED] = array(
            'name' => 'Orders completed',
            'description' => 'The total number of orders in completed state',
            'unit' => 'orders'
        );
    }

    public function resetOnCollect($key)
    {
        $resetKeys = array(
            self::KEY_ORDER_TOTAL_NEW,
            self::KEY_ORDER_AMOUNT_AVERAGE_NEW,
            self::KEY_ORDER_SIZE_TOTAL_NEW,
            self::KEY_ORDER_SIZE_AVERAGE_NEW,
            self::KEY_ORDER_AMOUNT_TOTAL_NEW,

        );

        if (in_array($key, $resetKeys)) {
            return true;
        }
        return false;
    }

    /**
     * Observe the adding of new orders to the system
     *
     * @param Varien_Event_Observer $observer
     */
    public function addNew(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getEvent()->getOrder();

        $amountUnit = Mage::getStoreConfig('currency/options/base', $order->getStoreId());

        $this->setMetric(
            self::ACTION_INCREMENT,
            self::KEY_ORDER_SIZE_TOTAL,
            $order->getStoreId(),
            $order->getTotalItemCount()
        );

        $this->setMetric(
            self::ACTION_INCREMENT,
            self::KEY_ORDER_AMOUNT_TOTAL,
            $order->getStoreId(),
            $order->getBaseGrandTotal(),
            $amountUnit
        );

        $this->setMetric(
            self::ACTION_INCREMENT,
            self::KEY_ORDER_TOTAL,
            $order->getStoreId(),
            1
        );

        $this->setMetric(
            self::ACTION_INCREMENT,
            self::KEY_ORDER_SIZE_TOTAL_NEW,
            $order->getStoreId(),
            $order->getTotalItemCount()
        );

        $this->setMetric(
            self::ACTION_INCREMENT,
            self::KEY_ORDER_AMOUNT_TOTAL_NEW,
            $order->getStoreId(),
            $order->getBaseGrandTotal(),
            $amountUnit
        );

        $this->setMetric(
            self::ACTION_INCREMENT,
            self::KEY_ORDER_TOTAL_NEW,
            $order->getStoreId(),
            1
        );

        // Update state statistics (only is changed)
        if ($order->getState() != $order->getOrigData('state')) {
            // Decrease order processing when previous state was processing
            if ($order->getOrigData('state') == 'new') {
                $this->setMetric(
                    self::ACTION_INCREMENT,
                    self::KEY_ORDER_STATE_NEW,
                    $order->getStoreId(),
                    -1
                );
            }
            // Decrease order processing when previous state was processing
            if ($order->getOrigData('state') == 'processing') {
                $this->setMetric(
                    self::ACTION_INCREMENT,
                    self::KEY_ORDER_STATE_PROCESSING,
                    $order->getStoreId(),
                    -1
                );
            }

            // Increase orders with the state new
            if ($order->getState() == 'new') {
                $this->setMetric(
                    self::ACTION_INCREMENT,
                    self::KEY_ORDER_STATE_NEW,
                    $order->getStoreId(),
                    1
                );
            }
            // Increase orders with the state processing
            if ($order->getState() == 'processing') {
                $this->setMetric(
                    self::ACTION_INCREMENT,
                    self::KEY_ORDER_STATE_PROCESSING,
                    $order->getStoreId(),
                    1
                );
            }
            // Increase orders with the state complete
            if ($order->getState() == 'complete') {
                $this->setMetric(
                    self::ACTION_INCREMENT,
                    self::KEY_ORDER_STATE_COMPLETED,
                    $order->getStoreId(),
                    1
                );
            }
        }

        $this->updateAvgOrderValues($order->getStoreId());
    }

    /**
     * Update Avarage values for order details
     *
     * @param $storeId
     */
    public function updateAvgOrderValues($storeId)
    {
        $amountUnit = Mage::getStoreConfig('currency/options/base', $storeId);

        $orderTotal = $this->getMetric(self::KEY_ORDER_TOTAL, $storeId);
        $orderItems = $this->getMetric(self::KEY_ORDER_SIZE_TOTAL, $storeId);
        $orderAmount = $this->getMetric(self::KEY_ORDER_AMOUNT_TOTAL, $storeId);

        $newOrderTotal = $this->getMetric(self::KEY_ORDER_TOTAL_NEW, $storeId);
        $newOrderItems = $this->getMetric(self::KEY_ORDER_SIZE_TOTAL_NEW, $storeId);
        $newOrderAmount = $this->getMetric(self::KEY_ORDER_AMOUNT_TOTAL_NEW, $storeId);

        $this->setMetric(
            self::ACTION_UPDATE,
            self::KEY_ORDER_SIZE_AVERAGE,
            $storeId,
            ($orderItems/$orderTotal),
            $amountUnit
        );

        $this->setMetric(
            self::ACTION_UPDATE,
            self::KEY_ORDER_AMOUNT_AVERAGE,
            $storeId,
            ($orderAmount/$orderTotal),
            $amountUnit
        );

        if ($newOrderTotal > 0) {
            $this->setMetric(
                self::ACTION_UPDATE,
                self::KEY_ORDER_SIZE_AVERAGE_NEW,
                $storeId,
                ($newOrderItems / $newOrderTotal),
                $amountUnit
            );

            $this->setMetric(
                self::ACTION_UPDATE,
                self::KEY_ORDER_AMOUNT_AVERAGE_NEW,
                $storeId,
                ($newOrderAmount / $newOrderTotal),
                $amountUnit
            );
        }
    }


    public function initOrderData()
    {
        $collection = Mage::getResourceModel('sales/order_collection');
        if (!is_object($collection)) {
            return;
        }
        $collection->getSelect()
            ->reset('columns')
            ->columns(array('amount' => 'SUM(main_table.base_grand_total)',
                'items' => 'SUM(main_table.total_item_count)',
                'store_id' => 'main_table.store_id',
                'state' => 'main_table.state',
                'count' => 'COUNT(*)'))
            ->group(array('main_table.store_id','main_table.state'));

        $data = array();
        foreach ($collection as $order) {
            if (!$order->getStoreId()) {
                continue;
            }
            if (!isset($data[$order->getStoreId()])) {
                $data[$order->getStoreId()] = array(
                    'items' => 0,
                    'amount' => 0,
                    'count' => 0,
                    'new' => 0,
                    'processing' => 0,
                    'complete' => 0
                );
            }
            $data[$order->getStoreId()]['items'] += $order->getItems();
            $data[$order->getStoreId()]['amount'] += $order->getAmount();
            $data[$order->getStoreId()]['count'] += $order->getCount();
            if ($order->getState() == 'new') {
                $data[$order->getStoreId()]['new'] += $order->getCount();
            }

            if ($order->getState() == 'processing') {
                $data[$order->getStoreId()]['processing'] += $order->getCount();
            }

            if ($order->getState() == 'complete') {
                $data[$order->getStoreId()]['complete'] += $order->getCount();
            }
        }

        foreach ($data as $storeId => $details) {
            $this->setMetric(
                self::ACTION_UPDATE,
                self::KEY_ORDER_SIZE_TOTAL,
                $storeId,
                $details['items']
            );

            $this->setMetric(
                self::ACTION_UPDATE,
                self::KEY_ORDER_AMOUNT_TOTAL,
                $storeId,
                $details['amount']
            );

            $this->setMetric(
                self::ACTION_UPDATE,
                self::KEY_ORDER_TOTAL,
                $storeId,
                $details['count']
            );

            $this->setMetric(
                self::ACTION_UPDATE,
                self::KEY_ORDER_STATE_NEW,
                $storeId,
                $details['new']
            );

            $this->setMetric(
                self::ACTION_UPDATE,
                self::KEY_ORDER_STATE_PROCESSING,
                $storeId,
                $details['processing']
            );

            $this->setMetric(
                self::ACTION_UPDATE,
                self::KEY_ORDER_STATE_COMPLETED,
                $storeId,
                $details['complete']
            );

            $this->updateAvgOrderValues($storeId);
        }
    }

    /**
     * Get Abandonned cart amounts
     * @return array
     */
    public function getAbandonnedCarts()
    {
        /** @var $collection Mage_Reports_Model_Resource_Quote_Collection */
        $collection = Mage::getResourceModel('reports/quote_collection');
        if (!is_object($collection)) {
            return array();
        }
        $collection->prepareForAbandonedReport(array());
        $collection->getSelect()
            ->columns(array('store_id' => 'main_table.store_id',
                'count' => 'COUNT(*)',
                'subtotal' => 'subtotal'))
            ->group('main_table.store_id');
        $output = array();
        foreach ($collection as $order) {
            $output[] = array(
                'name' => 'Abandonned carts',
                'unit' => 'orders',
                'value' => (float)$order->getCount(),
                'store_id' => (int)$order->getStoreId(),
                'type' => 'A'
            );
            $output[] = array(
                'name' => 'Total value of abandoned carts',
                'unit' => 'Amount',
                'value' => (float)$order->getSubtotal(),
                'store_id' => (int)$order->getStoreId(),
                'type' => 'A'
            );
        }
        return $output;
    }



    public function getEmailQueueSize()
    {
        $edition = method_exists('Mage', 'getEdition') ? Mage::getEdition():false;

        // Pre CE1.7 version => No e-mail queue available
        if (!$edition) {
            return array();
        }

        // Pre CE 1.9 version => No e-mail queue available
        if ($edition == 'Community' && version_compare(Mage::getVersion(), '1.9', '<')) {
            return array();
        }

        // Pre EE 1.14 version => No e-mail queue available
        if ($edition == 'Enterprise' && version_compare(Mage::getVersion(), '1.14', '<')) {
            return array();
        }
        $collection = Mage::getResourceModel('core/email_queue_collection');
        if (!is_object($collection)) {
            return array();
        }
        return array(array(
            'name' => 'Amount of messages in the e-mail queue',
            'unit' => 'messages',
            'value' => $collection->getSize(),
            'store_id' => 0,
            'type' => 'A'
        ));
    }
}
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

    protected $statusPendingPickPack = false;
    protected $statusPickPack = false;
    protected $statusCompletedPickPack = false;

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
     * Identifier for pick order and time calculation
     */
    const KEY_ORDER_STATE_PENDING_PICKPACK = 2040;
    const KEY_ORDER_STATE_CURRENT_PICKPACK = 2041;
    const KEY_ORDER_STATE_COMPLETED_PICKPACK = 2042;
    const KEY_START_PICKPACK = 2043;
    const KEY_PICKED_QTY = 2044;
    const KEY_PICKED_TIME = 2045;
    const KEY_AVGTIME_PICKPACK = 2046;
    const KEY_TIME_PENDING_PICKPACK = 2047;
    const KEY_TIME_CURRENT_PICKPACK = 2048;

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

        $this->_metricData[self::KEY_ORDER_STATE_PENDING_PICKPACK] = array(
            'name' => 'Orders pending pick/pack',
            'description' => 'The total number of orders in waiting for pick/pack state',
            'unit' => 'orders'
        );

        $this->_metricData[self::KEY_ORDER_STATE_CURRENT_PICKPACK] = array(
            'name' => 'Orders pick/pack',
            'description' => 'The total number of orders in pick/pack state',
            'unit' => 'orders'
        );

        $this->_metricData[self::KEY_ORDER_STATE_COMPLETED_PICKPACK] = array(
            'name' => 'Orders completed pick/pack',
            'description' => 'The total number of orders in completed pick/pack state',
            'unit' => 'orders'
        );

        $this->_metricData[self::KEY_PICKED_QTY] = array(
            'name' => 'Picked qty',
            'description' => 'The qty of orders picked',
            'unit' => 'qty'
        );

        $this->_metricData[self::KEY_PICKED_TIME] = array(
            'name' => 'Picked time',
            'description' => 'Total time to pick/pack',
            'unit' => 'seconds'
        );

        $this->_metricData[self::KEY_AVGTIME_PICKPACK] = array(
            'name' => 'Avg time pick/pack',
            'description' => 'Avg time to pick/pack an order',
            'unit' => 'seconds'
        );

        $this->_metricData[self::KEY_TIME_PENDING_PICKPACK] = array(
            'name' => 'Time pending pick/pack',
            'description' => 'The total time needed to pick/pack new orders',
            'unit' => 'seconds'
        );

        $this->_metricData[self::KEY_TIME_CURRENT_PICKPACK] = array(
            'name' => 'Time current pick/pack',
            'description' => 'The total time needed to pick/pack current orders in pick/pack state',
            'unit' => 'seconds'
        );

        $this->statusPendingPickPack = Mage::getStoreConfig('system/coscale_monitor/status_pickpack_pending');
        $this->statusPickPack = Mage::getStoreConfig('system/coscale_monitor/status_pickpack');
        $this->statusCompletedPickPack = Mage::getStoreConfig('system/coscale_monitor/status_pickpack_completed');
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
    public function salesOrderPlaceAfter(Varien_Event_Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return;
        }

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

        $this->updateAvgOrderValues($order->getStoreId());
    }

    /**
     * Save order status changes on order save
     *
     * @param Varien_Event_Observer $observer
     */
    public function salesOrderSaveCommitAfter(Varien_Event_Observer $observer)
    {
        $keys = array();
        if (!$this->_helper->isEnabled()) {
            return;
        }

        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getEvent()->getOrder();

        // Update state/status statistics (only if changed)
        if ($order->getState() != $order->getOrigData('state')) {
            // Decrease qty for previous state
            switch ($order->getOrigData('state')) {
                case Mage_Sales_Model_Order::STATE_NEW:
                    $keys[self::KEY_ORDER_STATE_NEW] = -1;
                    break;
                case Mage_Sales_Model_Order::STATE_PROCESSING:
                    $keys[self::KEY_ORDER_STATE_PROCESSING] = -1;
                    break;
            }
            // Increase qty for current state
            switch ($order->getData('state')) {
                case Mage_Sales_Model_Order::STATE_NEW:
                    $keys[self::KEY_ORDER_STATE_NEW] = 1;
                    break;
                case Mage_Sales_Model_Order::STATE_PROCESSING:
                    $keys[self::KEY_ORDER_STATE_PROCESSING] = 1;
                    break;
                case Mage_Sales_Model_Order::STATE_COMPLETE:
                    $keys[self::KEY_ORDER_STATE_COMPLETED] = 1;
                    break;
            }
        }

        if ($order->getStatus() != $order->getOrigData('status')) {
            // Decrease qty for previous state
            switch ($order->getOrigData('status')) {
                case $this->statusPendingPickPack:
                    $keys[self::KEY_ORDER_STATE_PENDING_PICKPACK] = -1;
                    break;
                case $this->statusPickPack:
                    $keys[self::KEY_ORDER_STATE_CURRENT_PICKPACK] = -1;
                    break;
                case $this->statusCompletedPickPack:
                    $keys[self::KEY_ORDER_STATE_COMPLETED_PICKPACK] = -1;
                    break;
            }
            // Increase qty for current state
            switch ($order->getData('status')) {
                case $this->statusPendingPickPack:
                    $keys[self::KEY_ORDER_STATE_PENDING_PICKPACK] = 1;
                    break;
                case $this->statusPickPack:
                    $keys[self::KEY_ORDER_STATE_CURRENT_PICKPACK] = 1;
                    break;
                case $this->statusCompletedPickPack:
                    $keys[self::KEY_ORDER_STATE_COMPLETED_PICKPACK] = 1;
                    break;
            }
        }
        if (count($keys)>0) {
            // Check if order picked data decreases
            if (isset($keys[self::KEY_ORDER_STATE_CURRENT_PICKPACK]) &&
                $keys[self::KEY_ORDER_STATE_CURRENT_PICKPACK]<0) {
                // Get difference between last timestamp and now (time used for orderpicking)
                $timeUsed = 1;
                if ($metricData = $this->getMetricData(self::KEY_PICKED_TIME, $order->getStoreId())) {
                    $currentDate = date('U', Mage::getModel('core/date')->timestamp(time()));
                    $lastDate = date('U', strtotime($metricData->getUpdatedAt()));
                    $timeUsed = $currentDate - $lastDate;
                }
                $keys[self::KEY_PICKED_TIME] = $timeUsed;
                $keys[self::KEY_PICKED_QTY] = 1;
            }

            foreach ($keys as $key => $qty) {
                if ($qty <> 0) {
                    $this->setMetric(
                        self::ACTION_INCREMENT,
                        $key,
                        $order->getStoreId(),
                        $qty
                    );
                }

            }
            $this->updateAvgPickValues($order->getStoreId());
        }

    }

    /**
     * Update pick/pack avarage values
     *
     * @param $storeId
     */
    public function updateAvgPickValues($storeId)
    {
        $pickTime = $this->getMetric(self::KEY_PICKED_TIME, $storeId);
        $pickQty = $this->getMetric(self::KEY_PICKED_QTY, $storeId);

        // Update avg pick time
        if ($pickQty>0) {
            $avgPickTime = floor($pickTime/$pickQty);
            $this->setMetric(
                self::ACTION_UPDATE,
                self::KEY_AVGTIME_PICKPACK,
                $storeId,
                $avgPickTime,
                'seconds'
            );

            $updateAvgKeys = array(
                self::KEY_ORDER_STATE_PENDING_PICKPACK=>self::KEY_TIME_PENDING_PICKPACK,
                self::KEY_ORDER_STATE_CURRENT_PICKPACK=>self::KEY_TIME_CURRENT_PICKPACK
            );
            // Update avg values for keys
            foreach ($updateAvgKeys as $from => $to) {
                $qty = $this->getMetric($from, $storeId);

                $this->setMetric(
                    self::ACTION_UPDATE,
                    $to,
                    $storeId,
                    ($qty*$avgPickTime),
                    'qty'
                );
            }
        }
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
        if (!$this->_helper->isEnabled()) {
            return;
        }

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
     * Generate output event
     *
     * @param Varien_Event_Observer $observer
     */
    public function generate(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();

        /** @var CoScale_Monitor_Helper_Data $logger */
        $logger = $event->getLogger();
        /** @var CoScale_Monitor_Model_Output_Generator $output */
        $output = $event->getOutput();

        // Get Abandonned Carts
        try {
            $logger->debugStart('AbandonnedCarts');

            $carts = $this->getAbandonnedCarts();
            foreach ($carts as $data) {
                $output->addMetric($data);
            }
            $logger->debugEnd('AbandonnedCarts');
        } catch (Exception $ex) {
            $logger->debugEndError('AbandonnedCarts', $ex);
        }

        // Get Email Queue Size
        try {
            $logger->debugStart('Email Queue Size');

            foreach ($this->getEmailQueueSize() as $data) {
                $output->addMetric($data);
            }
            $logger->debugEnd('Email Queue Size');
        } catch (Exception $ex) {
            $logger->debugEndError('Email Queue Size', $ex);
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
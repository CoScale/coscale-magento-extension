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

    protected $stateNewKey = '';
    protected $stateProcessingKey = '';
    protected $stateCompletedKey = '';
    protected $stateCanceledKey = '';
    protected $statusPendingPickPackKey = '';
    protected $statusPickPackKey = '';
    protected $statusCompletedPickPackKey = '';

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
    const KEY_ORDER_STATE_CANCELED = 2033;

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
     * Identifier for abandoned carts
     */
    const KEY_ABANDONED_CARTS = 2060;
    const KEY_ABANDONED_CARTS_VALUE_TOTAL = 2061;

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

        $this->_metricData[self::KEY_ORDER_STATE_CANCELED] = array(
            'name' => 'Orders canceled',
            'description' => 'The total number of orders in canceled state',
            'unit' => 'orders'
        );

        $this->_metricData[self::KEY_ORDER_STATE_PENDING_PICKPACK] = array(
            'name' => 'Orders pending pick/pack',
            'description' => 'The total number of orders in waiting for pick/pack state',
            'unit' => 'orders',
            'calctype' => CoScale_Monitor_Model_Metric::CALC_INSTANT,
            'combine' => true,
        );

        $this->_metricData[self::KEY_ORDER_STATE_CURRENT_PICKPACK] = array(
            'name' => 'Orders pick/pack',
            'description' => 'The total number of orders in pick/pack state',
            'unit' => 'orders',
            'calctype' => CoScale_Monitor_Model_Metric::CALC_INSTANT,
            'combine' => true,
        );

        $this->_metricData[self::KEY_ORDER_STATE_COMPLETED_PICKPACK] = array(
            'name' => 'Orders completed pick/pack',
            'description' => 'The total number of orders in completed pick/pack state',
            'unit' => 'orders',
            'calctype' => CoScale_Monitor_Model_Metric::CALC_INSTANT,
            'combine' => true,
        );

        $this->_metricData[self::KEY_PICKED_QTY] = array(
            'name' => 'Picked qty',
            'description' => 'The qty of orders picked',
            'unit' => 'qty',
            'calctype' => CoScale_Monitor_Model_Metric::CALC_INSTANT,
            'combine' => true,
        );

        $this->_metricData[self::KEY_PICKED_TIME] = array(
            'name' => 'Picked time',
            'description' => 'Total time to pick/pack',
            'unit' => 'seconds',
            'calctype' => CoScale_Monitor_Model_Metric::CALC_INSTANT,
            'combine' => true,
        );

        $this->_metricData[self::KEY_AVGTIME_PICKPACK] = array(
            'name' => 'Avg time pick/pack',
            'description' => 'Avg time to pick/pack an order',
            'unit' => 'seconds',
            'calctype' => CoScale_Monitor_Model_Metric::CALC_INSTANT,
            'combine' => true,
        );

        $this->_metricData[self::KEY_TIME_PENDING_PICKPACK] = array(
            'name' => 'Time pending pick/pack',
            'description' => 'The total time needed to pick/pack new orders',
            'unit' => 'seconds',
            'calctype' => CoScale_Monitor_Model_Metric::CALC_INSTANT,
            'combine' => true,
        );

        $this->_metricData[self::KEY_TIME_CURRENT_PICKPACK] = array(
            'name' => 'Time current pick/pack',
            'description' => 'The total time needed to pick/pack current orders in pick/pack state',
            'unit' => 'seconds',
            'calctype' => CoScale_Monitor_Model_Metric::CALC_INSTANT,
            'combine' => true,
        );

        $this->_metricData[self::KEY_ABANDONED_CARTS] = array(
            'name' => 'Abandonned carts',
            'description' => 'Abandonned carts',
            'unit' => 'orders',
        );

        $this->_metricData[self::KEY_ABANDONED_CARTS_VALUE_TOTAL] = array(
            'name' => 'Total value of abandoned carts',
            'description' => 'Total value of abandoned carts',
            'unit' => 'Amount',
        );

        $this->statusPendingPickPack = Mage::getStoreConfig('system/coscale_monitor/status_pickpack_pending');
        $this->statusPickPack = Mage::getStoreConfig('system/coscale_monitor/status_pickpack');
        $this->statusCompletedPickPack = Mage::getStoreConfig('system/coscale_monitor/status_pickpack_completed');

        $this->stateNewKey = Mage::getStoreConfig('system/coscale_monitor/state_new_key');
        $this->stateProcessingKey = Mage::getStoreConfig('system/coscale_monitor/state_processing_key');
        $this->stateCompletedKey = Mage::getStoreConfig('system/coscale_monitor/state_completed_key');
        $this->stateCanceledKey = Mage::getStoreConfig('system/coscale_monitor/state_canceled_key');
        $this->statusPendingPickPackKey = Mage::getStoreConfig('system/coscale_monitor/status_pickpack_pending_key');
        $this->statusPickPackKey = Mage::getStoreConfig('system/coscale_monitor/status_pickpack_key');
        $this->statusCompletedPickPackKey = Mage::getStoreConfig('system/coscale_monitor/status_pickpack_completed_key');
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
    * Initialize metrics in case they are not already set
    *
    * @param $storeId
    */
    public function initDefaultMetrics($storeId)
    {
        $initKeys = array(
            self::KEY_ORDER_TOTAL_NEW,
            self::KEY_ORDER_AMOUNT_AVERAGE_NEW,
            self::KEY_ORDER_SIZE_TOTAL_NEW,
            self::KEY_ORDER_SIZE_AVERAGE_NEW,
            self::KEY_ORDER_AMOUNT_TOTAL_NEW,
            self::KEY_ORDER_SIZE_AVERAGE,
            self::KEY_PICKED_QTY,
            self::KEY_PICKED_TIME,
            self::KEY_AVGTIME_PICKPACK,
            self::KEY_TIME_PENDING_PICKPACK,
            self::KEY_TIME_CURRENT_PICKPACK,
            self::KEY_ABANDONED_CARTS,
            self::KEY_ABANDONED_CARTS_VALUE_TOTAL,
        );

        $amountUnit = Mage::getStoreConfig('currency/options/base', $storeId);

        foreach ($initKeys as $key)
        {
            $metricData = $this->getMetric($key, $storeId);

            // 'Amount' unit of a metric should always be replaced by the store currency
            $unit = ($this->_metricData[$key]['unit'] == 'Amount' ? $amountUnit : $this->_metricData[$key]['unit']);
            if (empty($metricData)) {
                $this->setMetric(
                    self::ACTION_UPDATE,
                    $key,
                    $storeId,
                    0,
                    $unit
                );
            }
        }
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
    public function salesOrderSaveAfter(Varien_Event_Observer $observer)
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
                case Mage_Sales_Model_Order::STATE_CANCELED:
                    $keys[self::KEY_ORDER_STATE_CANCELED] = 1;
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
     * Update pick/pack average values
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
            ($orderItems/$orderTotal)
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
                ($newOrderItems / $newOrderTotal)
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
                    'complete' => 0,
                );
            }
            $data[$order->getStoreId()]['items'] += $order->getItems();
            $data[$order->getStoreId()]['amount'] += $order->getAmount();
            $data[$order->getStoreId()]['count'] += $order->getCount();
            if ($order->getState() == Mage_Sales_Model_Order::STATE_NEW) {
                $data[$order->getStoreId()]['new'] += $order->getCount();
            }

            if ($order->getState() == Mage_Sales_Model_Order::STATE_PROCESSING) {
                $data[$order->getStoreId()]['processing'] += $order->getCount();
            }

            if ($order->getState() == Mage_Sales_Model_Order::STATE_COMPLETE) {
                $data[$order->getStoreId()]['complete'] += $order->getCount();
            }

            if ($order->getState() == Mage_Sales_Model_Order::STATE_CANCELED) {
                $data[$order->getStoreId()]['canceled'] += $order->getCount();
            }

            if ($order->getState() == Mage_Sales_Model_Order::STATE_HOLDED) {
                $data[$order->getStoreId()]['holded'] += $order->getCount();
            }
        }
        $status_collection = Mage::getResourceModel('sales/order_collection');
        if (!is_object($status_collection)) {
            return;
        }
        $status_collection->getSelect()
            ->reset('columns')
            ->columns(array('store_id' => 'main_table.store_id',
                'status' => 'main_table.status',
                'count' => 'COUNT(*)'))
            ->group(array('main_table.store_id','main_table.status'));
        foreach ($status_collection as $order) {
            if (!$order->getStoreId()) {
                continue;
            }
            if (!isset($data[$order->getStoreId()])) {
                $data[$order->getStoreId()] = array(
                    'status_pending' => 0,
                    'status_processing' => 0,
                    'status_complete' => 0
                );
            }
            if ($order->getStatus() == $this->statusPendingPickPackKey) {
                $data[$order->getStoreId()]['status_pending'] += $order->getCount();
            }

            if ($order->getStatus() == $this->statusPickPackKey) {
                $data[$order->getStoreId()]['status_processing'] += $order->getCount();
            }

            if ($order->getStatus() == $this->statusCompletedPickPackKey) {
                $data[$order->getStoreId()]['status_complete'] += $order->getCount();
            }
        }

        foreach ($data as $storeId => $details) {
            $amountUnit = Mage::getStoreConfig('currency/options/base', $storeId);

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
                $details['amount'],
                $amountUnit
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

            $this->setMetric(
                self::ACTION_UPDATE,
                self::KEY_ORDER_STATE_CANCELED,
                $storeId,
                $details['canceled']
            );

            $this->setMetric(
                self::ACTION_UPDATE,
                self::KEY_ORDER_STATE_PENDING_PICKPACK,
                $storeId,
                $details['status_pending']
            );

            $this->setMetric(
                self::ACTION_UPDATE,
                self::KEY_ORDER_STATE_CURRENT_PICKPACK,
                $storeId,
                $details['status_processing']
            );

            $this->setMetric(
                self::ACTION_UPDATE,
                self::KEY_ORDER_STATE_COMPLETED_PICKPACK,
                $storeId,
                $details['status_complete']
            );

            $this->updateAvgOrderValues($storeId);
            $this->initDefaultMetrics($storeId);
        }
    }

    /**
     * Cronjob to update the orders metrics
     */
    public function dailyCron()
    {
        if (!$this->_helper->isEnabled()) {
            return;
        }
        $this->initOrderData();
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
                'subtotal' => 'SUM(subtotal)'))
            ->group('main_table.store_id');
        $output = array();
        foreach ($collection as $order) {
            $amountUnit = Mage::getStoreConfig('currency/options/base', (int)$order->getStoreId());

            $this->setMetric(
                self::ACTION_UPDATE,
                self::KEY_ABANDONED_CARTS,
                (int)$order->getStoreId(),
                (float)$order->getCount()
            );

            $this->setMetric(
                self::ACTION_UPDATE,
                self::KEY_ABANDONED_CARTS_VALUE_TOTAL,
                (int)$order->getStoreId(),
                (float)$order->getSubtotal()
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
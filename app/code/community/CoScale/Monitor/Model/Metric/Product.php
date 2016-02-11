<?php

/**
 * Observer for the metrics related to products/categories
 *
 * @package CoScale_Monitor
 * @author  Vladimir Kerkhoff <v.kerkhoff@genmato.com>
 * @version 1.0
 * @created 2015-08-18
 */
class CoScale_Monitor_Model_Metric_Product extends CoScale_Monitor_Model_Metric_Abstract
{
    /**
     * Identifier for the total number of products in the system
     */
    const KEY_PRODUCT_TOTAL = 3000;
    const KEY_PRODUCT_TODAY = 3001;
    /**
     * Identifier for the total number of categories in the system
     */
    const KEY_CATEGORIES_TOTAL = 3010;
    const KEY_CATEGORIES_TODAY = 3011;

    /**
     * Public contructor function
     */
    public function _contruct()
    {
        $this->_metricData[self::KEY_PRODUCT_TOTAL] = array(
            'name' => 'Products',
            'description' => 'The total number of products in the system',
            'unit' => 'products'
        );

        $this->_metricData[self::KEY_PRODUCT_TODAY] = array(
            'name' => 'New products today',
            'description' => 'The total number of products created today',
            'unit' => 'products'
        );

        $this->_metricData[self::KEY_CATEGORIES_TOTAL] = array(
            'name' => 'Categories',
            'description' => 'The total number of categories in the system',
            'unit' => 'categories'
        );

        $this->_metricData[self::KEY_CATEGORIES_TODAY] = array(
            'name' => 'New categories today',
            'description' => 'The total number of categories created today',
            'unit' => 'categories'
        );
    }

    /**
     * Observe the adding of new product to the system
     *
     * @param Varien_Event_Observer $observer
     */
    public function addNewProduct(Varien_Event_Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return;
        }

        /** @var Mage_Catalog_Model_Product $product */
        $product = $observer->getEvent()->getProduct();

        if ($product->getOrigData('entity_id') == $product->getId()) {
            return;
        }

        $this->setMetric(
            self::ACTION_INCREMENT,
            self::KEY_PRODUCT_TOTAL,
            0,
            1
        );

        $this->setMetric(
            self::ACTION_INCREMENT,
            self::KEY_PRODUCT_TODAY,
            0,
            1
        );
    }

    /**
     * Observe the remove of product
     *
     * @param Varien_Event_Observer $observer
     */
    public function removeProduct(Varien_Event_Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return;
        }

        $this->setMetric(
            self::ACTION_INCREMENT,
            self::KEY_PRODUCT_TOTAL,
            0,
            -1
        );
    }

    /**
     * Observe the adding of new category to the system
     *
     * @param Varien_Event_Observer $observer
     */
    public function addNewCategory(Varien_Event_Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return;
        }

        /** @var Mage_Catalog_Model_Category $category */
        $category = $observer->getEvent()->getCategory();

        if ($category->getOrigData('entity_id') == $category->getId()) {
            return;
        }

        $this->setMetric(
            self::ACTION_INCREMENT,
            self::KEY_CATEGORIES_TOTAL,
            0,
            1
        );

        $this->setMetric(
            self::ACTION_INCREMENT,
            self::KEY_CATEGORIES_TODAY,
            0,
            1
        );
    }

    /**
     * Observe the remove of category
     *
     * @param Varien_Event_Observer $observer
     */
    public function removeCategory(Varien_Event_Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return;
        }

        $this->setMetric(
            self::ACTION_INCREMENT,
            self::KEY_CATEGORIES_TOTAL,
            0,
            -1
        );
    }

    /**
     * Cronjob to update the total number of customers
     */
    public function dailyCron()
    {
        if (!$this->_helper->isEnabled()) {
            return;
        }
        $this->resetDayCounter();
        $this->updateTotalCount();
    }

    /**
     * Reset daily new created products counter
     */
    protected function resetDayCounter()
    {
        $this->setMetric(
            self::ACTION_UPDATE,
            self::KEY_PRODUCT_TODAY,
            0,
            0
        );

        $this->setMetric(
            self::ACTION_UPDATE,
            self::KEY_CATEGORIES_TODAY,
            0,
            0
        );
    }

    /**
     * Daily update full numbers of products
     */
    public function updateTotalCount()
    {
        $collection = Mage::getResourceModel('catalog/product_collection');
        if(!is_object($collection))
        {
        	return;
        }
        $this->setMetric(
            self::ACTION_UPDATE,
            self::KEY_PRODUCT_TOTAL,
            0,
            $collection->getSize()
        );

        $collection = Mage::getResourceModel('catalog/category_collection');
        if(!is_object($collection))
        {
        	return;
        }
        $this->setMetric(
            self::ACTION_UPDATE,
            self::KEY_CATEGORIES_TOTAL,
            0,
            $collection->getSize()
        );
    }
}
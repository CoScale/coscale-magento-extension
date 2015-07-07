<?php
/**
 * CoScale metric interaction model
 *
 * @package CoScale_Monitor
 * @author Rian Orie <rian.orie@supportdesk.nu>
 * @created 2015-07-03
 * @version 1.0
 *
 * @method $this setKey(int $metricId)
 * @method $this setStoreId(int $store)
 * @method $this setName(string $name)
 * @method $this setDescription(string $description)
 * @method $this setType(int $type)
 * @method $this setValue(string $value)
 * @method $this setUnit(string $unit)
 * @method $this setTimestamp(int $timestamp)
 * @method $this setUpdatedAt(string $date)
 * @method int getKey()
 * @method int getStoreId()
 * @method int getType()
 * @method string getName()
 * @method string getDescription()
 * @method mixed getValue()
 * @method string getUnit()
 * @method int getTimestamp()
 * @method string getUpdatedAt()
 */ 
class CoScale_Monitor_Model_Metric extends Mage_Core_Model_Abstract
{
	/**
	 * Type for server-based metrics
	 */
	const TYPE_SERVER = 1;

	/**
	 * Type for application-based metrics
	 */
	const TYPE_APPLICATION = 2;


	/**
	 * Identifier for total orders
	 */
	const ID_TOTAL_ORDERS = 1;

	/**
	 * Identifier for the total number of customers in the system
	 */
	const ID_TOTAL_CUSTOMERS = 2;



	/**
	 * Construct the metric model
	 */
    protected function _construct()
    {
        $this->_init('coscale_monitor/metric');
    }

	/**
	 * Shorthand to add or update a metric to the system
	 *
	 * @param int    $key         A key as predefined in this model
	 * @param int    $store       The store id
	 * @param int    $type        A predefined type for the metric
	 * @param string $name        The name of the metric
	 * @param string $description A longer description of the metric
	 * @param mixed  $value       The value to set
	 * @param string $unit        The unit the value is saved in
	 *
	 * @throws Exception
	 */
	public function updateMetric($key, $store, $type, $name, $description, $value, $unit)
	{
		if (is_null($this->getKey())) {
			$this->loadByKey($key, $store);
		}

		$this->setKey($key)
			 ->setStoreId($store)
			 ->setType($type)
			 ->setName($name)
			 ->setDescription($description)
			 ->setValue($value)
			 ->setUnit($unit);

		$this->save();
	}

	/**
	 * Shorthand to add or increment a numerical metric in the system
	 *
	 * @param int    $key         A key as predefined in this model
	 * @param int    $store       The store the metric belongs to
	 * @param string $type        A predefined type for the metric
	 * @param string $name        The name of the metric
	 * @param string $description A longer description of the metric
	 * @param mixed  $value       The value to set
	 * @param string $unit        The unit the value is saved in
	 */
	public function incrementMetric($key, $store, $type, $name, $description, $value, $unit)
	{
		$this->loadByKey($key, $store);

		$this->updateMetric($key, $store, $type, $name, $description, ($this->getValue()+$value), $unit);
	}

	/**
	 * Load a metric by it's key
	 *
	 * @param int $key   The predefined key to load the metric from
	 * @param int $store The store to load the metric for
	 *
	 * @return $this
	 *
	 * @throws Exception
	 */
	public function loadByKey($key, $store)
	{
		if ( ! in_array($key, $this->getMetrics())) {
			throw new Exception('Key should be one of the predefined keys');
		}

		$this->_getResource()->loadByKey($this, $key, $store);
		return $this;
	}

	/**
	 * Return an array of the available metrics
	 *
	 * @return array
	 */
	protected function getMetrics()
	{
		return array(self::ID_TOTAL_ORDERS,
		             self::ID_TOTAL_CUSTOMERS);
	}

	/**
	 * Set some defaults before saving an event to the database
	 *
	 * @return $this
	 */
	protected function _beforeSave()
	{
		$date = Mage::getModel('core/date');

		$this->setTimestamp($date->timestamp())
		     ->setUpdatedAt($date->date());

		return $this;
	}
}
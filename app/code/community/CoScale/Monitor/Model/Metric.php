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
 * @method $this setName(string $name)
 * @method $this setDescription(string $description)
 * @method $this setGroups(string $groups)
 * @method $this setValue(string $value)
 * @method $this setUnit(string $unit)
 * @method $this setTags(string $tags)
 * @method $this setTimestamp(int $timestamp)
 * @method $this setUpdatedAt(string $date)
 * @method int getKey()
 * @method int getDatatype()
 * @method string getName()
 * @method string getDescription()
 * @method string getGroups()
 * @method string getValue()
 * @method string getUnit()
 * @method string getTags()
 * @method int getTimestamp()
 * @method int getCalctype()
 * @method string getUpdatedAt()
 */ 
class CoScale_Monitor_Model_Metric extends Mage_Core_Model_Abstract
{
	/**
	 * Double datatype
	 */
	const DATATYPE_DOUBLE = 'DOUBLE';

	/**
	 * Result will be the difference between new and old
	 */
	const CALCTYPE_DIFFERENCE = 1;

	/**
	 * Result will be the new value
	 */
	const CALCTYPE_INSTANT = 2;

	/**
	 * Result will be average per read
	 */
	const CALCTYPE_AVERAGE = 3;

	/**
	 * Result will be percentage of the difference between new and old relative to the time interval
	 */
	const CALCTYPE_DIFFERENCE_PERCENTAGE = 4;

	/**
	 * Result will be the invers of average
	 */
	const CALCTYPE_INVERS_AVERAGE = 5;

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
	 * Make sure the version is always an integer, even when it's null
	 *
	 * @return int
	 */
	public function getVersion()
	{
		if (is_null($this->getData('version'))) {
			return 0;
		}

		return $this->getData('version');
	}

	/**
	 * Set the datatype for this metric
	 *
	 * @param string $type One of the predefined data types
	 *
	 * @return $this
	 *
	 * @throws Exception
	 */
	public function setDatatype($type)
	{
		if ( ! in_array($type, array(self::DATATYPE_DOUBLE))) {
			throw new Exception('Datatype should be one of the predefined options.');
		}

		return $this->setData('datatype', $type);
	}

	/**
	 * Set the calculation type
	 *
	 * @param int $type One of the predefined calculation types
	 *
	 * @throws Exception
	 *
	 * @return $this
	 */
	public function setCalctype($type)
	{
		if ( ! in_array($type, array(self::CALCTYPE_DIFFERENCE, self::CALCTYPE_INSTANT, self::CALCTYPE_AVERAGE,
		                             self::CALCTYPE_DIFFERENCE_PERCENTAGE, self::CALCTYPE_INVERS_AVERAGE))) {
			throw new Exception('Calculation type should be one of the predefined values.');
		}

		return $this->setData('calctype', $type);
	}

	/**
	 * Shorthand to add or update a metric to the system
	 *
	 * @param int    $key         A key as predefined in this model
	 * @param string $datatype    A predefined datatype for the value
	 * @param string $name        The name of the metric
	 * @param string $description A longer description of the metric
	 * @param mixed  $value       The value to set
	 * @param string $unit        The unit the value is saved in
	 * @param int    $calctype    The calculation type for this metric
	 * @param string $groups      Groups this metric belongs to
	 * @param string $tags        Tags for this metric
	 *
	 * @throws Exception
	 */
	public function updateMetric($key, $datatype, $name, $description, $value, $unit, $calctype, $groups = null, $tags = null)
	{
		if (is_null($this->getKey())) {
			$this->loadByKey($key);
		}

		$this->setKey($key)
			 ->setDatatype($datatype)
			 ->setName($name)
			 ->setDescription($description)
			 ->setCalctype($calctype)
			 ->setValue($value)
			 ->setUnit($unit)
			 ->setGroups($groups)
			 ->setTags($tags);

		$this->save();
	}

	/**
	 * Shorthand to add or increment a numerical metric in the system
	 *
	 * @param int    $key         A key as predefined in this model
	 * @param string $datatype    A predefined datatype for the value
	 * @param string $name        The name of the metric
	 * @param string $description A longer description of the metric
	 * @param mixed  $value       The value to set
	 * @param string $unit        The unit the value is saved in
	 * @param string $groups      Groups this metric belongs to
	 * @param string $tags        Tags for this metric
	 */
	public function incrementMetric($key, $datatype, $name, $description, $value, $unit, $groups = null, $tags = null)
	{
		$this->loadByKey($key);

		$oldValue = intval($this->getValue());

		$this->updateMetric($key, $datatype, $name, $description, ($oldValue+$value), $unit, self::CALCTYPE_INSTANT, $groups, $tags);
	}

	/**
	 * Load a metric by it's key
	 *
	 * @param int $key The predefined key to load the metric from
	 *
	 * @return $this
	 *
	 * @throws Exception
	 */
	public function loadByKey($key)
	{
		if ( ! in_array($key, array(self::ID_TOTAL_CUSTOMERS, self::ID_TOTAL_ORDERS))) {
			throw new Exception('Key should be one of the predefined keys');
		}

		$this->_getResource()->loadByKey($this, $key);
		return $this;
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
		     ->setUpdatedAt($date->date())
		     ->setVersion($this->getVersion()+1);

		return $this;
	}
}
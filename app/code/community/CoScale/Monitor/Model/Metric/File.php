<?php
/**
 * Metrics related to files
 *
 * @package CoScale_Monitor
 * @author  Vladimir Kerkhoff <v.kerkhoff@genmato.com>
 * @version 1.0
 * @created 2015-08-18
 */
class CoScale_Monitor_Model_Metric_File extends CoScale_Monitor_Model_Metric_Abstract
{
    public function generate(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();

        /** @var CoScale_Monitor_Helper_Data $logger */
        $logger = $event->getLogger();
        /** @var CoScale_Monitor_Model_Output_Generator $output */
        $output = $event->getOutput();

        // Get Error reports
        try {
            $logger->debugStart('Error Reports');

            $output->addMetric($this->getErrorReports());

            $logger->debugEnd('Error Reports');
        } catch (Exception $ex) {
            $logger->debugEndError('Error Reports', $ex);
        }

        // Get Logfiles (name and size)
        try {
            $logger->debugStart('Logfiles');

            foreach ($this->getLogFiles() as $data) {
                $output->addMetric($data);
            }
            $logger->debugEnd('Logfiles');
        } catch (Exception $ex) {
            $logger->debugEndError('Logfiles', $ex);
        }
    }

    /**
     * Get amount of reports in var/report
     * @return array
     */
    public function getErrorReports()
    {
        $dir = Mage::getBaseDir('var') . DS . 'report';

        $contents = scandir($dir);

        return array(
            'name' => 'Files in var/report/',
            'unit' => 'files',
            'value' => (count($contents)-2),
            'store_id' => 0,
            'type' => 'S'
        );
    }

    /**
     * Get details of logfiles in var/log
     * @return array
     */
    public function getLogFiles()
    {
        $dir = Mage::getBaseDir('var') . DS . 'log';

        $contents = scandir($dir);
        $output = array();
        foreach ($contents as $logfile) {
            if ($logfile == '.' || $logfile=='..') {
                continue;
            }
            $output[] = array(
                'name' => 'Logfile var/log/' . $logfile,
                'unit' => 'bytes',
                'value' => filesize($dir . DS . $logfile),
                'store_id' => 0,
                'type' => 'S'
            );
        }
        return $output;
    }
}
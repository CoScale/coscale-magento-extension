<?php

$importPath = dirname ( $argv[0] );

require_once $importPath . '/abstract.php';

/**
 * CoScale shell script that will output the different elements of the module
 *
 * @package     CoScale_Monitor
 * @author      Rian Orie <rian.orie@supportdesk.nu>
 * @created     2015-07-03
 * @version     1.0
 */
class CoScale_Shell extends Mage_Shell_Abstract
{
    protected $debug = false;

    /**
     * Execute the script
     */
    public function run()
    {
        $helper = Mage::helper('coscale_monitor');

        if ($this->getArg('debug')) {
            $helper->enableDebug();
        }

        if (!$helper->isEnabled()) {
            echo json_encode(array('error'=>'CoScale Module not active!' . ' ' . $helper->getLogs()));
            return;
        }

        // Generate output
        try {
            $helper->debugStart('Output Generation');
            echo Mage::getSingleton('coscale_monitor/output_generator')->getJsonOutput();
            $helper->debugEnd('Output Generation');
        } catch (Exception $ex) {
            $helper->debugEndError('Output Generation', $ex);
        }
    }
}

$shell = new CoScale_Shell();
$shell->run();

<?php
 
class Jakey_ResellBlocker_Model_Resource_Basetime extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('jakey_resellblocker/basetime', 'id');
    }
}
?>
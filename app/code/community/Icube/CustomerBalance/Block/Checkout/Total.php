<?php
/**
 * Customer balance total block for checkout
 *
 */
class Icube_CustomerBalance_Block_Checkout_Total extends Mage_Checkout_Block_Total_Default
{
    /**
     * @var string
     */
    protected $_template = 'customerbalance/checkout/total.phtml';
}

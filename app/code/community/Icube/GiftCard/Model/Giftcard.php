<?php
class Icube_GiftCard_Model_Giftcard extends Mage_Core_Model_Abstract
{
    const XML_PATH                    = 'giftcard/general/';
    const XML_PATH_EMAIL              = 'giftcard/email/';

    const XML_PATH_IS_REDEEMABLE      = 'giftcard/general/is_redeemable';
    const XML_PATH_LIFETIME           = 'giftcard/general/lifetime';
    const XML_PATH_ORDER_ITEM_STATUS  = 'giftcard/general/order_item_status';
    const XML_PATH_ALLOW_MESSAGE      = 'giftcard/general/allow_message';
    const XML_PATH_MESSAGE_MAX_LENGTH = 'giftcard/general/message_max_length';
    const XML_PATH_EMAIL_IDENTITY     = 'giftcard/email/identity';
    const XML_PATH_EMAIL_TEMPLATE     = 'giftcard/email/template';

    const TYPE_VIRTUAL  = 0;
    const TYPE_PHYSICAL = 1;
    const TYPE_COMBINED = 2;

    const OPEN_AMOUNT_DISABLED = 0;
    const OPEN_AMOUNT_ENABLED  = 1;
}

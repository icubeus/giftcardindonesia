<?php
class Icube_GiftCardAccount_Model_Cron
{
    /**
     * Update Gift Card Account states by cron
     *
     * @return Icube_GiftCardAccount_Model_Cron
     */
    public function updateStates()
    {
        // update to expired
        $model = Mage::getModel('icube_giftcardaccount/giftcardaccount');

        $now = Mage::getModel('core/date')->date('Y-m-d');

        $collection = $model->getCollection()
            ->addFieldToFilter('state', Icube_GiftCardAccount_Model_Giftcardaccount::STATE_AVAILABLE)
            ->addFieldToFilter('date_expires', array('notnull'=>true))
            ->addFieldToFilter('date_expires', array('lt'=>$now));

        $ids = $collection->getAllIds();
        if ($ids) {
            $state = Icube_GiftCardAccount_Model_Giftcardaccount::STATE_EXPIRED;
            $model->updateState($ids, $state);
        }
        return $this;
    }
}

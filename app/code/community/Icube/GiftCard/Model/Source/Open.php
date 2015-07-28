<?php
class Icube_GiftCard_Model_Source_Open extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $result = array();
        foreach ($this->_getValues() as $k => $v) {
            $result[] = array(
                'value' => $k,
                'label' => $v,
            );
        }

        return $result;
    }

    /**
     * Get option text
     *
     * @return string|null
     */
    public function getOptionText($value)
    {
        $options = $this->_getValues();
        if (isset($options[$value])) {
            return $options[$value];
        }
        return null;
    }

    /**
     * Get values
     *
     * @return array
     */
    protected function _getValues()
    {
        return array(
            Icube_GiftCard_Model_Giftcard::OPEN_AMOUNT_DISABLED => Mage::helper('icube_giftcard')->__('No'),
            Icube_GiftCard_Model_Giftcard::OPEN_AMOUNT_ENABLED  => Mage::helper('icube_giftcard')->__('Yes'),
        );
    }

    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColums()
    {
        $attributeDefaultValue = $this->getAttribute()->getDefaultValue();
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $attributeType = $this->getAttribute()->getBackendType();
        $isNullable = is_null($attributeDefaultValue) || empty($attributeDefaultValue);

        $column = array(
            'unsigned' => false,
            'extra'    => null,
            'default'  => $isNullable ? null : $attributeDefaultValue
        );

        if (Mage::helper('core')->useDbCompatibleMode()) {
            $column['type']     = $attributeType;
            $column['is_null']  = $isNullable;
        } else {
            $column['type']     = Mage::getResourceHelper('eav')->getDdlTypeByColumnType($attributeType);
            $column['nullable'] = $isNullable;
            $column['comment']  = 'Icube Giftcard Open ' . $attributeCode . ' column';
        }

        return array($attributeCode => $column);
    }

    /**
     * Retrieve select for flat attribute update
     *
     * @param int $store
     * @return Varien_Db_Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return Mage::getResourceModel('eav/entity_attribute')
            ->getFlatUpdateSelect($this->getAttribute(), $store);
    }
}

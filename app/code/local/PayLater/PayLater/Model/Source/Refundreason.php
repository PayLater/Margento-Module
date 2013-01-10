<?php


class PayLater_PayLater_Model_Source_Refundreason
{
    /**
     * Get list of paylater refund reasons as array
     *
     * @return array
     */
    public function toOptionArray()
    {   
        $options = array();
        foreach (explode(PayLater_PayLater_Core_Interface::REASON_CODES_SEPARATOR, PayLater_PayLater_Core_Interface::REASON_CODES) as $code) {
			if(!$code){
				continue;
			}
			$reason_const = "REASON_{$code}_REASON";
            $option = array(
                'value' => $code,
                'label' => constant('PayLater_PayLater_Core_Interface::'.$reason_const),
            );
            array_push($options, $option);
        }
        
        return $options;
    }
}
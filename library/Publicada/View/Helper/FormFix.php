<?php

class Publicada_View_Helper_FormFix {
    public function formFix( Zend_Form $_form )
	{
        foreach( $_form->getElements() as $element ) {
            if( $element instanceof Zend_Form_Element_Hidden ) {
                $label = $element->getLabel();
                if( empty($label) ) {
                    $element->setLabel( '&nbsp;' );
                }
                foreach( $element->getDecorators() as $decorator ) {
                    $decorator->setOption( 'class', 'hidden' );
                }
            }
        }
        return $_form;
    }
}
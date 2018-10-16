<?php
class tableTablesPts extends tablePts {
    public function __construct() {
        $this->_table = '@__tables';
        $this->_id = 'id';
        $this->_alias = 'sup_tables';
        $this->_addField('unique_id', 'text', 'text')
				->_addField('label', 'text', 'text')
				->_addField('original_id', 'text', 'int')
				->_addField('params', 'text', 'text')
				->_addField('html', 'text', 'text')
				->_addField('css', 'text', 'text')
				->_addField('img', 'text', 'text')
				->_addField('sort_order', 'text', 'int')
				->_addField('is_base', 'text', 'int')
				->_addField('is_pro', 'text', 'int');
    }
}
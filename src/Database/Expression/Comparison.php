<?php
/**
 * PHP Version 5.4
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 3.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
namespace Cake\Database\Expression;

use Cake\Database\ExpressionInterface;
use Cake\Database\ValueBinder;

/**
 * A Comparison is a type of query expression that represents an operation
 * involving a field an operator and a value. In its most common form the
 * string representation of a comparison is `field = value`
 *
 */
class Comparison extends QueryExpression {

/**
 * The field name or expression to be used in the left hand side of the operator
 *
 * @var string
 */
	protected $_field;

/**
 * The value to be used in the right hand side of the operation
 *
 * @var mixed
 */
	protected $_value;

/**
 * The type to be used for casting the value to a database representation
 *
 * @var string
 */
	protected $_type;

/**
 * Constructor
 *
 * @param string $field the field name to compare to a value
 * @param mixed $value the value to be used in comparison
 * @param string $type the type name used to cast the value
 * @param string $conjunction the operator used for comparing field and value
 * @return void
 */
	public function __construct($field, $value, $type, $conjuntion) {
		$this->_field = $field;
		$this->_value = $value;
		$this->type($conjuntion);

		if (is_string($type)) {
			$this->_type = $type;
		}
		if (is_string($field) && isset($types[$this->_field])) {
			$this->_type = current($types);
		}

		$this->_conditions[$field] = $value;
	}

/**
 * Sets the field name
 *
 * @param string $field
 * @return void
 */
	public function field($field) {
		$this->_field = $field;
	}

/**
 * Sets the value
 *
 * @param mixed $value
 * @return void
 */
	public function value($value) {
		$this->_value = $value;
	}

/**
 * Returns the field name
 *
 * @return string
 */
	public function getField() {
		return $this->_field;
	}

/**
 * Returns the value used for comparison
 *
 * @return mixed
 */
	public function getValue() {
		return $this->_value;
	}

/**
 * Convert the expression into a SQL fragment.
 *
 * @param Cake\Database\ValueBinder $generator Placeholder generator object
 * @return string
 */
	public function sql(ValueBinder $generator) {
		if ($this->_value instanceof ExpressionInterface) {
			$template = '%s %s (%s)';
			$value = $this->_value->sql($generator);
		} else {
			list($template, $value) = $this->_stringExpression($generator);
		}
		return sprintf($template, $this->_field, $this->_conjunction, $value);
	}

/**
 * Returns a template and an placeholder for the value after registering it
 * with the placeholder $generator
 *
 * @param ValueBinder $generator
 * @return array First position containing the template and the second a placeholder
 */
	protected function _stringExpression($generator) {
		if (strpos($this->_type, '[]') !== false) {
			$template = '%s %s (%s)';
			$value = $this->_flattenValue($generator);
		} else {
			$template = '%s %s %s';
			$value = $this->_bindValue($generator, $this->_value, $this->_type);
		}
		return [$template, $value];
	}

/**
 * Registers a value in the placeholder generator and returns the generated placeholder
 *
 * @param ValueBinder $generator
 * @param mixed $value
 * @param string $type
 * @return string generated placeholder
 */
	protected function _bindValue($generator, $value, $type) {
		$placeholder = $generator->placeholder($this->_field);
		$generator->bind($placeholder, $value, $type);
		return $placeholder;
	}

/**
 * Converts a traversable value into a set of placeholders generated by
 * $generator and separated by `,`
 *
 * @param ValueBinder $generator
 * @return string
 */
	protected function _flattenValue($generator) {
		$parts = [];
		$type = str_replace('[]', '', $this->_type);
		foreach ($this->_value as $value) {
			$parts[] = $this->_bindValue($generator, $value, $type);
		}
		return implode(',', $parts);
	}

/**
 * Returns the number of expression this class represents
 *
 * @return integer
 */
	public function count() {
		return 1;
	}

}
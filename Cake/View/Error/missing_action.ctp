<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
use Cake\Core\Configure;
use Cake\Utility\Inflector;
use Cake\Core\Plugin;

$namespace = Configure::read('App.namespace');
if (!empty($plugin)) {
	$namespace = $plugin;
}
$prefixNs = '';
if (!empty($prefix)) {
	$prefix = Inflector::camelize($prefix);
	$prefixNs = '\\' . $prefix;
}
if (empty($plugin)) {
	$path = APP_DIR . DS . 'Controller' . DS . $prefix . DS . h($controller) . '.php' ;
} else {
	$path = Plugin::path($plugin) . 'Controller' . DS . $prefix . DS . h($class) . '.php';
}
?>
<h2><?= __d('cake_dev', 'Missing Method in %s', h($controller)); ?></h2> <p class="error">
	<strong><?= __d('cake_dev', 'Error'); ?>: </strong>
	<?= __d('cake_dev', 'The action %1$s is not defined in controller %2$s', '<em>' . h($action) . '</em>', '<em>' . h($controller) . '</em>'); ?>
</p>
<p class="error">
	<strong><?= __d('cake_dev', 'Error'); ?>: </strong>
	<?= __d('cake_dev', 'Create %1$s%2$s in file: %3$s.', '<em>' . h($controller) . '::</em>', '<em>' . h($action) . '()</em>', $path); ?>
</p>
<pre>
&lt;?php
namespace <?= h($namespace); ?>\Controller<?= h($prefixNs); ?>;

use <?= h($namespace); ?>\Controller\AppController;

class <?= h($controller); ?> extends AppController {

<strong>
	public function <?= h($action); ?>() {

	}
</strong>
}
</pre>
<p class="notice">
	<strong><?= __d('cake_dev', 'Notice'); ?>: </strong>
	<?= __d('cake_dev', 'If you want to customize this error message, create %s', APP_DIR . DS . 'View' . DS . 'Error' . DS . 'missing_action.ctp'); ?>
</p>
<?= $this->element('exception_stack_trace'); ?>
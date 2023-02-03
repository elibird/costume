<?php

namespace modules\costume;

use Craft;
use yii\base\Module;

use yii\boostrap5;

class Costume extends Module
{

	// Public Methods
    // =========================================================================

    public function init(): void
    {
        // Call the `Module::init()` method, which will do its own initializations
        parent::init();
        
        // Define a custom alias using the module ID
        Craft::setAlias('@costume', __DIR__);
        
        // Your custom code will go here
        //Craft::dd('Module loaded!');

/* 		=======================================================
 *      bootstrap sanity test to ensure bootstrap itself works: 
 * 		=======================================================

        echo <<<EOF
        <html>        
        <head>
        <title>Bootstrap test</title>
        <script src="/assets/bootstrap.min.js"></script>
        <link rel="stylesheet" href="/assets/bootstrap.css" />
        </head>
        <body>
EOF;
        
        //okay, the following works.
        //$progress = \yii\bootstrap5\Progress::widget(['percent' => 60, 'label' => 'test']);        
        //var_dump($progress);        
        //echo $progress;
        
        echo <<<EOF
        </body>
        </html>
EOF;
*/

		// === TESTING: === //
		
		// just using 'yiisoft/yii2-bootstrap5' for now, but .. (this code would work to pull in any yii2 widget and make available to twig)
	
		$src_folder = reset(\Yii::$app->extensions['yiisoft/yii2-bootstrap5']['alias']); /// CAREFUL, alias could be an array, this is just test code
		
		$namespace_might_be = str_replace('@', '', array_key_first(\Yii::$app->extensions['yiisoft/yii2-bootstrap5']['alias']));
		
		$namespace_might_be = '\\' . (str_replace('/', '\\', $namespace_might_be)) . '\\';
		
		//echo "<pre>";
		//var_dump($namespace_might_be);
		//echo "</pre>";		
		//die();
				
		if (is_dir($src_folder)) {
			
			$files = scandir($src_folder);
			
			foreach ($files as $file) {
			
				if (substr($file, -4) === '.php') {
			
					$className = substr($file, 0, -4);
					
					//echo "className = $className <br>";
					
					$a_string = $className;
					
					$fully_qualified = $namespace_might_be . $className;
					
					try {
						//$object = new $className();
						
						//$object = new $fully_qualified; 
						
						// instead of instantiating, try to get the parent class
						
						//$parent = get_parent_class($fully_qualified); 
						
						$parents = class_parents($fully_qualified);  // need *all* the parents if we're checking for a class that extends from Widget
						
						//echo "parent classes = ". (print_r($parents, true)) . " <br>";
						
						//$className = String($className);
						
						if(in_array('yii\base\Widget', $parents)){
							
							//echo " === EXTENDS WIDGET ===<br>";						
							
							$twig_extension_classname = "{$className}TwigExtension";
							
							//$the_name = strtolower("{$className}");
							
							$a_string = strtolower($a_string);
							
							//die($a_string);							
							//echo 'the_name = ' . $a_string . "<br>";
							
							/* ---- CODE TO WRIITE ---- */
							$code = <<<EOF
							use Twig\Extension\AbstractExtension;
							use Twig\TwigFunction;
							
							class {$twig_extension_classname} extends AbstractExtension
{

public function getName()
    {
        return '{$twig_extension_classname}';
    }

public function getFunctions()
    {
        return [
            new TwigFunction('yii2{$a_string}', [\$this, 'doit']),
        ];
    }
    
public function doit(\$options)
{        
	return $fully_qualified::widget(\$options);
}

}
							
EOF;
							
							//echo "$code<br><br>";
							//die('?');
							
							/* ---- END CODE TO WRITE ---- */
							
							eval($code);
							Craft::$app->view->registerTwigExtension(new $twig_extension_classname()); //classname will be dynamic
							
						} else {
							//echo " === DOES NOT EXTEND WIDGET ===<br>";
						}
						
						//echo "<br><br>";
						
						//echo "Class $className instantiated successfully.\n";
						
					} catch (\Exception $e) {
						//echo "Error instantiating class $className: " . $e->getMessage() . "\n";
					}
				}
			}
		}
		
		
		//die();
		
		//Craft::dd(\Yii::$app->extensions);
		
		//$extensions = \Yii::$app->getModules();
		
		//foreach ($extensions as $id => $extension) {
			//echo "Extension ID: $id\n";			
			//echo "Extension Class: " . get_class($extension) . "\n"; // doesn't work for PHP 8? //string instead of object			
			// "Extension ID: costume"			
		//}
        
        //chatbot's attempt at searching extension folders for extensions. -- yii2-extensions folder does not exist, though
        //$extensionPath = \Yii::getAlias('@vendor/yiisoft/yii2-extensions');
        /*
        var_dump($extensionPath);
        die();
        
		if (is_dir($extensionPath)) {
			$extensions = scandir($extensionPath);
			foreach ($extensions as $extension) {
				if ($extension === '.' || $extension === '..') {
					continue;
				}
				echo "Extension ID: $extension\n";
			}
		}
        */
        
        //Craft::dd('Module loaded!');
        
    }
}

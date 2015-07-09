<?php

	namespace App\Core;
	
	use \App\Core\FileSystem;
	use \App\Core\File;
	
	use \Assetic\AssetManager;
	use \Assetic\AssetWriter;
	
	use \Assetic\Asset\AssetCollection;
	use \Assetic\Asset\FileAsset;
	use \Assetic\Asset\GlobAsset;
	
	use \Assetic\Filter\CssMinFilter;
	use \Assetic\Filter\CssRewriteFilter;
	use \Assetic\Filter\JSMinFilter;
	use \Assetic\Filter\LessphpFilter;
	use \Assetic\Filter\Sass\SassFilter;
	use \Assetic\Filter\Sass\ScssFilter;
	
	class Assets{
		
		public function __construct($config){
			
			$this -> config = $config;
			
		}
		
		public function build($directory){
				
			# Writer
			
			$this -> writer = new AssetWriter($directory);
			
			/*
			 * App
			*/
			
			if(isset($this -> config['app']['directory'])){
				
				# CSS
				
				$css = new AssetCollection(array(
					new GlobAsset($this -> config['app']['directory'].'/css/*.css'),
					new GlobAsset($this -> config['app']['directory'].'/less/*.less', array(
						new LessphpFilter()
					))
				), array(
					//new CssRewriteFilter(),
					//new CssMinFilter()
				));
				
				$css -> setTargetPath('css/app.css');
				$this -> writer -> writeAsset($css);
				
				# JS
				
				$js = new AssetCollection(array(), array(
					//new JSMinFilter()
				));
				
				if(file_exists($this -> config['app']['directory'].'/js/app.modules.js'))
				
					$js -> add(new FileAsset($this -> config['app']['directory'].'/js/app.modules.js'));
				
				foreach(FileSystem::scandir($this -> config['app']['directory'].'/js', true) as $asset){
				
					if($asset != $this -> config['app']['directory'].'/js/app.modules.js')
				
						$js -> add(new FileAsset($asset));
					
				}
				
				$js -> setTargetPath('js/app.js');
				$this -> writer -> writeAsset($js);
				
				# Fonts
				
				$fonts = new AssetCollection(array(
					new GlobAsset($this -> config['app']['directory'].'/fonts/*')
				));
				
				foreach($fonts as $asset){
					
					$asset -> setTargetPath('fonts/'.$asset -> getSourcePath());
				
					$this -> writer -> writeAsset($asset);
					
				}
				
			}
			
			/*
			 * Bower
			*/
			
			if(isset($this -> config['bower']['config'])){
			
				$root = dirname(dirname(__DIR__));
				
				# CSS
				
				$css = new AssetCollection(array(), array(
					//new CssRewriteFilter(),
					//new CssMinFilter()
				));
				
				$css -> setTargetPath('css/vendor.css');
				
				# JS
				
				$js = new AssetCollection(array(), array(
					//new JSMinFilter()
				));
				
				$js -> setTargetPath('js/vendor.js');
				
				# Fonts
				
				$fonts = new AssetCollection(array());
					
				# Check the bower directory
					
				if(isset($this -> bower['bowerrc']['directory']))
				
					$root .= '/'.$this -> bower['bowerrc']['directory'];
					
				else
				
					$root .= '/bower_components';
					
				# Check the packages
				
				foreach(FileSystem::scandir($root, false, true) as $path){
					
					$package = basename($path);
					
					# Check if component.json or bower.json
					
					/*
					
					if(is_file($path.'/component.json'))
					
						$bower = json_decode(file_get_contents($path.'/component.json'), true);
					
					else 
					
					*/
					
					if(is_file($path.'/.bower.json'))
					
						$bower = json_decode(file_get_contents($path.'/.bower.json'), true);
						
					else if(is_file($path.'/bower.json'))
					
						$bower = json_decode(file_get_contents($path.'/bower.json'), true);
						
					else
					
						continue;
						
					# Merge the overrides
						
					if(isset($bower['name']) && isset($this -> config['bower']['config']['overrides'][$bower['name']]))
					
						$bower = array_merge($bower, $this -> config['bower']['config']['overrides'][$bower['name']]);
						
					else if(isset($this -> config['bower']['config']['overrides'][$package]))
					
						$bower = array_merge($bower, $this -> config['bower']['config']['overrides'][$package]);
						
					# Add the package
						
					$packages[(isset($bower['name']) ? $bower['name']:$package)] = array('path' => $path, 'bower' => $bower);
					
				}
				
				# Sort packages (for dependencies)
				
				$Sort = new Sort();
				
				foreach($packages as $key => $package){
					
					$dependencies = array();
					
					if(isset($package['bower']['dependencies'])){
						
						foreach($package['bower']['dependencies'] as $_key => $tag)
						
							$dependencies[] = $_key;
						
					}
					
					$Sort -> addNode($key, $dependencies);
					
				}
				
				$keys = $Sort -> getSortedNodes();
				
				# Merge all the assets in the correct order
				
				$assets = array();
				
				foreach($keys as $key){
					
					$array = (is_array($packages[$key]['bower']['main']) ? $packages[$key]['bower']['main']:array($packages[$key]['bower']['main']));
					
					foreach($array as $asset)
					
						$assets[] = $packages[$key]['path'].'/'.$asset;
				
				}
				
				# Check Assets
					
				foreach($assets as $asset){
				
					if(!is_file($asset))
					
						continue;
						
					$ext = pathinfo($asset, PATHINFO_EXTENSION);
					
					switch($ext){
						
						# CSS
						
						case 'css' :
						
							$css -> add(new FileAsset($asset));
						
						break;
						
						# LESS
						
						case 'less' :
						
							$css -> add(new FileAsset($asset), array(new LessphpFilter()));
						
						break;
						
						# SASS :
						
						case 'sass' :

							$css -> add(new FileAsset($asset), array(new SassFilter()));

						break;
						
						# SCSS
						
						case 'scss' :

							$css -> add(new FileAsset($asset), array(new ScssFilter()));

						break;
						
						# JS
						
						case 'js' :
						
							$js -> add(new FileAsset($asset));
						
						break;
						
						# Fonts
						
						case 'eot' :
						case 'svg' :
						case 'ttf' :
						case 'woff' :
						case 'woff2' :
						
							$fonts -> add(new FileAsset($asset));
						
						break;
						
					}
					
				}
				
				# Write
				
				$this -> writer -> writeAsset($css);
				$this -> writer -> writeAsset($js);
				
				foreach($fonts as $asset){
					
					$asset -> setTargetPath('fonts/'.$asset -> getSourcePath());
				
					$this -> writer -> writeAsset($asset);
					
				}
				
			}
			
		}
		
	}
	
?>
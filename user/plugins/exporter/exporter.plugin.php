<?php
namespace Habari;

class Exporter extends Plugin
{	
	public function filter_autoload_dirs($dirs) {
		$dirs[] = __DIR__ . '/classes';
		return $dirs;
	}
	
	private static function move_assets( $template_dir, $destination_path, $assets ) {
		foreach( $assets as $asset ) {
			copy( $template_dir . '/' . $asset, $destination_path . '/' . $asset );
		}
	}
	
	private static function save($filename, $export_dir, $folder, $file) {
		try {
			$fp = fopen( Site::get_path('user') . '/' . $export_dir . '/' . $folder . '/' . $filename, 'w' );
			if( $fp == false ) {
				$fp = fopen( Site::get_path('user') . '/' . $export_dir . '/' . $folder . '/' . $filename, 'w' );
			} else {
				fwrite($fp, $file);
				fclose($fp);
			}
		} catch( Exception $e ) {
			echo $e->getMessage();
			exit();
		}
	}

	public static function generate(Array $args, $type) {
		switch( $type ) {
			case 'html' :
				self::parseHTML( $args );
			break;
			case 'pdf' :
				self::parsePDF( $args );
			break;
		}
	}

	private static function parseHTML(Array $args) {
		$objects = $args['objects'];
		$template_dir = $args['template_location'];
		$export_dir = $args['export_location'];
		$templates = $args['template_types'];
		$connected = $args['connected'] ? $args['connected']['items'] : false;
		$assets = $args['assets'];
		$menu = '';
		
		Common::create_dir( Site::get_path('user') . '/' . $export_dir . '/' . $args['export_name'] );

		if( $connected != false ) {
			$parent = $args['connected']['parent'];
			$contents = $objects[$connected]['content'];
			
			if( $contents instanceof Posts ) {
				foreach( $contents as $post ) {
					$menu .= '<li><a href="' . $post->slug . '.html">' . $post->title . '</a></li>';
				}
			}
		}

		foreach( $templates as $template ) {
			$contents = $objects[$template]['content'];			
			if( $contents instanceof Posts ) {
				foreach( $contents as $post ) {
					$file = file_get_contents( $template_dir . '/' . $template . '.html' );
					foreach($objects[$template]['fields'] as $field ) {
						$file = str_replace( "{" . $field . "}", $post->$field, $file );
					}
					
					if( $connected == true ) {
						$file = str_replace( "{d.title}", $parent->title, $file );
						$file = str_replace( "{d.link}", 'index.html', $file );
						$file = str_replace( "{pages}", $menu, $file );
					}

					$filename = $post->slug . '.html';

					self::save( $filename, $export_dir, $args['export_name'], $file );
				}
			} else {
				$file = file_get_contents( $template_dir . '/' . $template . '.html' );
				$post = $objects[$template]['content'];
				
				foreach($objects[$template]['fields'] as $field ) {
					$file = str_replace( '{' . $field . '}', $post->$field, $file );
				}
				
				$file = str_replace( "{link}", 'index.html', $file );
			}

			if( $connected == true ) {
				$file = str_replace( "{pages}", $menu, $file );
			}

			if( $post->slug == $args['export_name'] ) {
				$filename = 'index.html';
			} else {
				$filename = $post->slug . '.html';
			}
						
			self::save( $filename, $export_dir, $args['export_name'], $file );
		}
		
		self::move_assets( $template_dir, Site::get_path('user') . '/' . $export_dir . '/' . $args['export_name'], $assets );
	}
	
	private static function parsePDF(Array $args) {
		$objects = $args['objects'];
		$template_dir = $args['template_location'];
		$export_dir = $args['export_location'];
		$templates = $args['template_types'];
		$connected = $args['connected'] ? $args['connected']['items'] : false;
		$assets = $args['assets'];
		$rawPDF = file_get_contents( $template_dir . '/pdf.header.html' );
		
		$rawPDF = str_replace( "{siteurl}", Site::get_url('theme'), $rawPDF );
		
		include_once( __DIR__ . '/classes/dompdf_config.inc.php' );
		
		Common::create_dir( Site::get_path('user') . '/' . $export_dir . '/' . $args['export_name'] );		

		foreach( $templates as $template ) {
			$contents = $objects[$template]['content'];
			if( $contents instanceof Posts ) {
				
				foreach( $contents as $post ) {
					$file = file_get_contents( $template_dir . '/pdf.html' );
					
					foreach($objects[$template]['fields'] as $field ) {
						$file = str_replace( "{" . $field . "}", $post->$field, $file );
					}
					
					$rawPDF .= $file;
				}
			} else {
				$file = file_get_contents( $template_dir . '/pdf.html' );
				$post = $objects[$template]['content'];
				
				foreach($objects[$template]['fields'] as $field ) {
					$file = str_replace( '{' . $field . '}', $post->$field, $file );
				}
				
				$rawPDF .= $file;
			}
		}

		$rawPDF .= $file = file_get_contents( $template_dir . '/pdf.footer.html' );

		try {
			$dompdf = new \DOMPDF();
			$dompdf->load_html( $rawPDF );
			$dompdf->render();
			$dompdf->stream( $export_dir . '.pdf' );
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
}
?>
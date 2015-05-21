<?php

/**
 * 图片处理
 * @package logic
 * @name image.logic.php
 * @version 1.0
 */

class ImageProcesserLogic
{
	
	public function thumb($image_source, $image_dest, $width, $height)
	{
		handler('image')->setSrcImg($image_source);
		handler('image')->setDstImg($image_dest);
		handler('image')->createImg($width, $height);
		return $image_dest;
	}
	
	public function sthumb($image_source, $image_dest, $twidth, $theight)
	{
		require_once 'wimage.logic.php';
		handler('image')->setSrcImg($image_source);
		handler('image')->setDstImg($image_dest);
		$image = new Image();
		$image->open($image_source);
		$width = $image->width();
		$height = $image->height();
		if(round($width/125)>1 && round($height/85)>1 ){
			$image->thumb($twidth*2, $theight*2,Image::IMAGE_THUMB_SCALE)->save($image_dest);
			$image->open($image_dest);
			$image->thumb($twidth, $theight,Image::IMAGE_THUMB_CENTER)->save($image_dest);
			return $image_dest;
		}else{
			$image->thumb($twidth, $theight,Image::IMAGE_THUMB_CENTER)->save($image_dest);
			return $image_dest;
		}
	}
	
	public function water($image_source, $image_dest, $config)
	{
		if ($config['type'] == 'image')
		{
						handler('image')->setSrcImg($image_source);
			handler('image')->setDstImg($image_dest);
			handler('image')->setMaskImg($config['image']);
			handler('image')->setMaskPosition($config['position']);
			handler('image')->createImg(100);
		}
		elseif ($config['type'] == 'text')
		{
			if (ENC_IS_GBK)
			{
								$config['text'] = ENC_G2U($config['text']);
			}
						$config['text'] = mb_convert_encoding($config['text'], 'html-entities', 'UTF-8');
 			$r = array();
						$r[] = handler('image')->setSrcImg($image_source);
			$r[] = handler('image')->setDstImg($image_dest);
			$r[] = handler('image')->setMaskFont(ROOT_PATH.'static/images/watermark/'.$config['font']);
			$r[] = handler('image')->setMaskFontColor('#ffffff');
			$r[] = handler('image')->setMaskFontSize($config['fontsize'] ? $config['fontsize'] : 13);
			$r[] = handler('image')->setMaskWord($config['text']);
			$r[] = handler('image')->setMaskPosition($config['position']);
			$r[] = handler('image')->createImg(100);
		}
	}
}

?>
<?PHP
$textsize = 10;
$font_file = APPPATH.'/views/arial.ttf';
$bold_font_file = APPPATH.'/views/arial_bold.ttf';

$margin = 1;
$textmargin = 5;
$timewidth = 65;

$boxwidth = 120;
$boxheight = 50;

$boxx = $margin;
$boxy = $margin;


$row_count = 0;
foreach($grid as $row)
{
	if($row->row_occupied)
	{
		$row_count++;
	}
}
$image_height = $row_count * (2*$margin + $boxheight) + $margin;
$image_width = 3*$margin + $timewidth + 6 * ($boxwidth + 2*$margin);

$im = imagecreatetruecolor($image_width, $image_height);
$white = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);
$black = imagecolorallocate($im, 0x00, 0x00, 0x00);
$gray = imagecolorallocate($im, 0xE0, 0xE0, 0xE0);
$gray_border = imagecolorallocate($im, 0xA0, 0xA0, 0xA0);

$class_colors = array(
	imagecolorallocate($im, 0xF7, 0x42, 0x2B),
	imagecolorallocate($im, 0xFF, 0xAD, 0x46),
	imagecolorallocate($im, 0xB1, 0xE9, 0xD2),
	imagecolorallocate($im, 0x9F, 0xC6, 0xE7),
	imagecolorallocate($im, 0x7B, 0xA7, 0xED),
	imagecolorallocate($im, 0xB9, 0x9A, 0xFF),
);
$class_pos = 0;
foreach($schedule->classes as $call_number => $row)
{
	$schedule->classes[$call_number]->color = $class_colors[$class_pos%6];
	$class_pos++;
}

imagefill($im,0,0,$gray_border);
foreach($grid as $row)
{
	if($row->row_occupied)
	{
		imagefilledrectangle($im, $boxx, $boxy, $boxx+$timewidth, $boxy+$boxheight, $gray);
		
		$time_text = $row->start_datetime->format('g:i')."\n".$row->end_datetime->format('g:i');
		$tb = imagettfbbox($textsize, 0, $bold_font_file, $time_text);
		$text_x = ceil(($timewidth - $tb[2]) / 2);
		$text_y = ceil(($boxheight + $tb[5]+$textsize) / 2);
		
		imagefttext($im, $textsize, 0, $boxx+$text_x, $boxy+$text_y, $black, $bold_font_file, 
				$time_text);
		
		$boxx += $timewidth + 2*$margin;
		foreach($row->blocks as $block)
		{
			if($block === NULL)
			{
				imagefilledrectangle($im, $boxx, $boxy, $boxx+$boxwidth, $boxy+$boxheight, $gray);
			}
			else if($block !== FALSE)
			{
				$course = $schedule->classes[$block->call_number];
				$time = $schedule->times[$block->time_index];
				
				imagefilledrectangle($im, $boxx, $boxy, $boxx+$boxwidth, 
					$boxy+($boxheight+2*$margin)*$block->rowspan-2*$margin, $course->color);
				
				$course_time_text = $time->start_datetime->format('g:i')."-".$time->end_datetime->format('g:i');
				$tb = imagettfbbox($textsize, 0, $bold_font_file, $time_text);
				imagefttext($im, $textsize, 0, $boxx+$textmargin, $boxy+$textmargin+$textsize, $black, $bold_font_file, 
					$course_time_text);
				
				$course_text_y = (-1)*$tb[5];
				
				$text = $course->abbreviation.'-'.$course->course_number.' '.
						$course->section_number."\n".$time->room;
				$text = wordwrap($text,15,"\n",TRUE);
				
				imagefttext($im, $textsize, 0, $boxx+$textmargin, $boxy+$textmargin+$textsize+$course_text_y, $black, $font_file, 
					$text);
				
			}
			$boxx += $boxwidth + 2*$margin;
		}
		$boxx = $margin;
		
		$boxy += $boxheight;
		$boxy += 2*$margin;
	}
}

if(empty($filename))
{
	// Display the image
	header("Content-type: image/png"); 
	imagepng($im);
}
else
{
	imagepng($im,$filename);
}
imagedestroy($im);
<?PHP
$im = imagecreatetruecolor(1024, 1024);
$white = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);
$black = imagecolorallocate($im, 0x00, 0x00, 0x00);
$gray = imagecolorallocate($im, 0xCC, 0xCC, 0xCC);
$green = imagecolorallocate($im, 0x9A, 0xE3, 0xC5);

$textsize = 13;
$font_file = APPPATH.'/views/arial.ttf';

$margin = 4;
$timemargin = 25;
$timewidth = 100;

$boxwidth = 150;
$boxheight = 50;

$boxx = $margin;
$boxy = $margin;
foreach($grid as $row)
{
	
	imagefilledrectangle($im, $boxx, $boxy, $boxx+$timewidth, $boxy+$boxheight, $white);

	imagefttext($im, $textsize, 0, $boxx+$timemargin, $boxy+$timemargin, $black, $font_file, 
			$row->start_datetime->format('g:i')."\n".$row->end_datetime->format('g:i'));
	
	$boxx += $timewidth + $margin;
	foreach($row->blocks as $block)
	{
		if($block === NULL)
		{
			imagefilledrectangle($im, $boxx, $boxy, $boxx+$boxwidth, $boxy+$boxheight, $gray);
		}
		else if($block !== FALSE)
		{
			imagefilledrectangle($im, $boxx, $boxy, $boxx+$boxwidth, 
				$boxy+($boxheight+$margin)*$block->rowspan-$margin, $green);
			
			$course = $schedule->classes[$block->call_number];
			$time = $schedule->times[$block->time_index];
			
			$text = $course->abbreviation.'-'.$course->course_number.' '.
					$course->section_number."\n".$time->room;
			$text = wordwrap($text,15,"\n",TRUE);
			
			imagefttext($im, $textsize, 0, $boxx+$margin, $boxy+$margin+$textsize, $black, $font_file, 
				$text);
			
		}
		$boxx += $boxwidth + $margin;
	}
	$boxx = $margin;
	
	$boxy += $boxheight;
	$boxy += $margin;
}

// Display the image
header("Content-type: image/png"); 
imagepng($im);
imagedestroy($im);
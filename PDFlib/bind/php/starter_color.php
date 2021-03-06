<?php
# $Id: starter_color.php,v 1.11.2.1 2013/07/05 11:56:16 rp Exp $
# Starter color:
# Demonstrate the basic use of supported color spaces
#
# Apply the following color spaces to text and vector graphics:
# - gray
# - rgb
# - cmyk
# - iccbasedgray/rgb/cmyk
# - spot
# - lab
# - pattern
# - shadings
#
# Required software: PDFlib/PDFlib+PDI/PPS 7
# Required data: none



# This is where the data files are. Adjust as necessary.
$searchpath = dirname(dirname(__FILE__)).'/data';
$outfile = "";
$title = "Starter Color";

$y = 800;
$x = 50;
$xoffset1=80;
$xoffset2 = 100;
$yoffset = 70;
$r = 30;

try {
    # create a new PDFlib object
    $p = new PDFlib();

    $p->set_option("SearchPath={{" . $searchpath . "}}");

    # This means we must check return values of load_font() etc.
    $p->set_option("errorpolicy=return");

    /* Enable the following line if you experience crashes on OS X
     * (see PDFlib-in-PHP-HowTo.pdf for details):
     * $p->set_option("usehostfonts=false");
     */

    # all strings are expected as utf8
    $p->set_option("stringformat=utf8");

    if ($p->begin_document("", "") == 0) {
	die("Error: " . $p->get_errmsg());
    }

    $p->set_info("Creator", "PDFlib Cookbook");
    $buf = $title .  '  $Revision: 1.11.2.1 $';
    $p->set_info("Title", $buf);

    # Load the font
    $font = $p->load_font("Helvetica", "unicode", "");

    if ($font == 0) {
	die("Error: " . $p->get_errmsg());
    }

    # Start the page
    $p->begin_page_ext(0, 0, "width=a4.width height=a4.height");

    $p->setfont($font, 14);


    # -------------------------------------------------------------------
    # Use default colors
    #
    # If no special color is set the default values will be used. The
    # default values are restored at the beginning of the page.
    # 0=black in the Gray color space is the default fill and stroke
    # color in many cases, as shown in our sample.
    # -------------------------------------------------------------------
    

    # Fill a circle with the default black fill color
    $p->circle($x, $y-=$yoffset, $r);
    $p->fill();

    # Output text with default black fill color
    $p->fit_textline(
	    "Circle and text filled with default color {gray 0}",
	    $x+$xoffset2, $y, "");

    $p->fit_textline("1.", $x+$xoffset1, $y, "");

    # -------------------------------------------------------------------
    # Use the Gray color space
    #
    # Gray color is defined by Gray values between 0=black and 1=white.
    # -------------------------------------------------------------------
    

    # Using setcolor(), set the current fill color to a light gray
    # represented by (0.5, 0, 0, 0) which defines 50% gray. Since gray
    # colors are defined by only one value, the last three function
    # parameters must be set to 0.
    
    $p->setcolor("fill", "gray", 0.5, 0, 0, 0);

    # Fill a circle with the current fill color defined above
    $p->circle($x, $y-=$yoffset, $r);
    $p->fill();

    # Output text with the current fill color
    $p->fit_textline("Circle and text filled with {gray 0.5}",
	    $x+$xoffset2, $y, "");

    # Alternatively, you can set the fill color in the call to
    # fit_textline() using the "fillcolor" option. This case applies the
    # fill color just the single function call. The current fill color
    # won't be affected.
    
    $p->fit_textline("2.", $x+$xoffset1, $y, "fillcolor={gray 0.5}");


    # --------------------------------------------------------------------
    # Use the RGB color space
    #
    # RGB color is defined by RGB triples, i.e. three values between 0 and
    # 1 specifying the percentage of red, green, and blue.
    # (0, 0, 0) is black and (1, 1, 1) is white. The commonly used RGB
    # color values in the range 0?255 must be divided by 255 in order to
    # scale them to the range 0?1 as required by PDFlib.
    # --------------------------------------------------------------------
    

    # Use setcolor() to set the fill color to a grass-green
    # represented by (0.1, 0.95, 0.3, 0) which defines 10% red, 95% green,
    # 30% blue. Since RGB colors are defined by only three values, the last
    # function parameter must be set to 0.
    
    $p->setcolor("fill", "rgb", 0.1, 0.95, 0.3, 0);

    # Draw a circle with the current fill color defined above
    $p->circle($x, $y-=$yoffset, $r);
    $p->fill();

    # Output a text line with the RGB fill color defined above
    $p->fit_textline("Circle and text filled with {rgb 0.1 0.95 0.3}",
	    $x+$xoffset2, $y, "");

    # Alternatively, you can set the fill color in the call to
    # fit_textline() using the "fillcolor" option. This case applies the
    # fill color just the single function call. The current fill color
    # won't be affected.
    
    $p->fit_textline("3.", $x+$xoffset1, $y,
	    "fillcolor={rgb 0.1 0.95 0.3}");


    # --------------------------------------------------------------------
    # Use the CMYK color space
    #
    # CMYK color is defined by four CMYK values between 0 = no color and
    # 1 = full color representing cyan, magenta, yellow, and black values;
    # (0, 0, 0, 0) is white and (0, 0, 0, 1) is black.
    # --------------------------------------------------------------------
    

    # Use setcolor() to set the current fill color to a pale
    # orange, represented by (0.1, 0.7, 0.7, 0.1) which defines 10% Cyan,
    # 70% Magenta, 70% Yellow, and 10% Black.
    
    $p->setcolor("fill", "cmyk", 0.1, 0.7, 0.7, 0.1);

    # Fill a circle with the current fill color defined above
    $p->circle($x, $y-=$yoffset, $r);
    $p->fill();

    # Output a text line with the CMYK fill color defined above
    $p->fit_textline(
	    "Circle and text filled with {cmyk 0.1 0.7 0.7 0.1}",
	    $x+$xoffset2, $y, "");

    # Alternatively, you can set the fill color in the call to
    # fit_textline() using the "fillcolor" option. This case applies the
    # fill color just the single function call. The current fill color
    # won't be affected.
    
    $p->fit_textline("4.", $x+$xoffset1, $y,
	    "fillcolor={cmyk 0.1 0.7 0.7 0.1}");


    # --------------------------------------------------------------------
    # Use a Lab color
    #
    # Device-independent color in the CIE L*a*b* color space is specified
    # by a luminance value in the range 0-100 and two color values in the
    # range -127 to 128. The first value contains the green-red axis,
    # while the second value contains the blue-yellow axis.
    # --------------------------------------------------------------------
    

    # Set the current fill color to a loud blue, represented by
    # (100, -127, -127, 0). Since Lab colors are defined by only three
    # values, the last function parameter must be set to 0.
    
    $p->setcolor("fill", "lab", 100, -127, -127, 0);

    # Fill a circle with the fill color defined above
    $p->circle($x, $y-=$yoffset, $r);
    $p->fill();

    # Output a text line with the Lab fill color defined above
    $p->fit_textline("Circle and text filled with {lab 100 -127 -127}",
	    $x+$xoffset2, $y, "");

    # Alternatively, you can set the fill color in the call to
    # fit_textline() using the "fillcolor" option. This case applies the
    # fill color just the single function call. The current fill color
    # won't be affected.
    
    $p->fit_textline("5.", $x+$xoffset1, $y,
	    "fillcolor={lab 100 -127 -127}");


    # ---------------------------------------------------------------
    # Use an ICC based color
    #
    # ICC-based colors are specified with the help of an ICC profile.
    # ---------------------------------------------------------------
    

    # Load the sRGB profile. sRGB is guaranteed to be always available
    $icchandle = $p->load_iccprofile("sRGB", "usage=iccbased");

    # Set the color based on the sRGB ICC profile to a grass-green,
    # represented by the RGB color values (0.1 0.95 0.3) which
    # define 10% Red, 95% Green, and 30% Blue.
    #
    # You can use the same syntax for CMYK and grayscale profiles with
    # the corresponding number of four or one color values.
    $p->set_graphics_option("fillcolor={iccbased " . $icchandle
    		.  " 0.1 0.95 0.3}");
    
    # Fill a circle with the ICC based RGB fill color defined above #
    $p->circle($x, $y-=$yoffset, $r);
    $p->fill();
    
    # Output a text line with the ICC based RGB fill color defined above.
    $p->fit_textline(
    	"Circle and text filled with {iccbased srgb 0.1 0.95 0.3}",
    	$x+$xoffset2, $y, "");
    
    # Alternatively, you can set the fill color in the call to
    # fit_textline() using the "fillcolor" option. This case applies the
    # fill color just the single function call. The current fill color
    # won't be affected.
    #
    # The sRGB profile can also be specified directly with a keyword,
    # which makes the explicit loading of the profile with
    # PDF_load_iccprofile() unnecessary.
    $p->fit_textline("6.", $x+$xoffset1, $y,
        "fillcolor={iccbased srgb 0.1 0.95 0.3}");
        

    # --------------------------------------------------------------------
    # Use a spot color
    #
    # Spot color (separation color space) is a predefined or arbitrarily
    # named custom color with an alternate representation in one of the
    # other color spaces above; this is generally used for preparing
    # documents which are intended to be printed on an offset printing
    # machine with one or more custom colors. The tint value (percentage)
    # ranges from 0 = no color to 1 = maximum intensity of the spot color.
    # --------------------------------------------------------------------
    

    # Define the spot color "PANTONE 281 U" from the builtin color
    # library PANTONE
    
    $spot = $p->makespotcolor("PANTONE 281 U");

    # Set the spot color "PANTONE 281 U" with a tint value of 1 (=100%)
    # and output some text. Since spot colors are defined by only two
    # values, the last two function parameters must be set to 0.
    
    $p->setcolor("fill", "spot", $spot, 1.0, 0, 0);

    # Fill a circle with the ICC based RGB fill color defined above
    $p->circle($x, $y-=$yoffset, $r);
    $p->fill();

    $p->fit_textline(
	    "Circle and text filled with {spotname {PANTONE 281 U} 1}",
	    $x+$xoffset2, $y, "");

    # Alternatively, you can set the fill color in the call to
    # fit_textline() using the "fillcolor" option. This case applies the
    # fill color just the single function call. The current fill color
    # won't be affected.
    
    $p->fit_textline("7.", $x+$xoffset1, $y,
	"fillcolor={spotname {PANTONE 281 U} 1}");

    # or
    $buf = "fillcolor={spot " . $spot . " 1}";
    $p->fit_textline("7.", $x+$xoffset1, $y, $buf);


    # ----------------------------------------------------------
    # For using the Pattern color space, see the Cookbook topics
    # graphics/fill_pattern and images/background_pattern.
    # ----------------------------------------------------------
    

    # ---------------------------------------------------------
    # For using the Shading color space, see the Cookbook topic
    # color/color_gradient.
    # ---------------------------------------------------------
   

    $p->end_page_ext("");

    $p->end_document("");

    $buf = $p->get_buffer();
    $len = strlen($buf);

    header("Content-type: application/pdf");
    header("Content-Length: $len");
    header("Content-Disposition: inline; filename=starter_color.pdf");
    print $buf;

}
catch (PDFlibException $e) {
    die("PDFlib exception occurred in starter_color starter_color:\n" .
        "[" . $e->get_errnum() . "] " . $e->get_apiname() . ": " .
        $e->get_errmsg() . "\n");
}
catch (Exception $e) {
    die($e);
}

$p = 0;

?>

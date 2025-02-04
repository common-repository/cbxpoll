<?php
if (!defined('WPINC')) {
    die;
}


/**
 * Email template for cbxpoll
 *
 * Class CBXPollEmailTemplate
 */
class CBXPollEmailTemplate
{

    public $mail_format;
    public $settings_api;

    public function __construct($format = 'html')
    {
        $this->mail_format  = $format;
        $this->settings_api = new CBXPoll_Settings();
    }

    public function cbxpoll_mail_content_type($content_type = 'html')
    {
        $format = $this->mail_format;

        return CBXPollHelper::cbxppoll_mail_content_type($format);
    }

    public function emailGetHeader()
    {
        $direction   = 'ltr'; //$doc->direction; //todo
        $sitename    = get_bloginfo('name'); //$config->get('sitename');
        $headerimage = $this->settings_api->get_option('headerimage', 'cbxpoll_email_setting', '');

        $html = '
                    <!DOCTYPE html>
                    <html dir="'.$direction.'">
                        <head>
                            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                            <title>'.$sitename.'</title>
                    </head>
                    <body '.(($direction == 'ltr') ? ' leftmargin="0" ' : ' rightmargin="0"  ').'  marginwidth="0" topmargin="0" marginheight="0" offset="0">
                    <div id="wrapper" dir="'.$direction.'">
                        <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
                            <tr>
                                <td align="center" valign="top">
                                    <div id="template_header_image">'.(($headerimage != '') ? '<p style="margin-top:0;"><img src="'.$headerimage.'" alt="'.$sitename.'" /></p>' : '').'
                                    </div>
                                    <table border="0" cellpadding="0" cellspacing="0" id="template_container">
                                        <tr>
                                            <td align="center" valign="top">
                                                <!-- Header -->
                                                <table border="0" cellpadding="0" cellspacing="0"  id="template_header">
                                                    <tr>
                                                        <td id="header_wrapper">
                                                            <h1>{emailheading}</h1>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <!-- End Header -->
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center" valign="top">
                                                <!-- Body -->
                                                <table border="0" cellpadding="0" cellspacing="0"  id="template_body">
                                                    <tr>
                                                        <td valign="top" id="body_content">
                                                            <!-- Content -->
                                                            <table border="0" cellpadding="20" cellspacing="0" width="100%">
                                                                <tr>
                                                                    <td valign="top">
                                                                        <div id="body_content_inner">';

        return $html;

    }

    public function emailGetFooter()
    {
        $direction   = 'ltr'; //$doc->direction; //todo
        $sitename    = get_bloginfo('name'); //$config->get('sitename');
        $headerimage = $this->settings_api->get_option('headerimage', 'cbxpoll_email_setting', '');
        $footertext  = str_replace('{sitename}', get_bloginfo('name'),
            $this->settings_api->get_option('footertext', 'cbxpoll_email_setting', '{sitename}'));

        $html = '										            </div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <!-- End Content -->
                                                        </td>
                                                    </tr>
                                                </table>
                                                <!-- End Body -->
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center" valign="top">
                                                <!-- Footer -->
                                                <table border="0" cellpadding="10" cellspacing="0"  id="template_footer">
                                                    <tr>
                                                        <td valign="top">
                                                            <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                                                <tr>
                                                                    <td colspan="2" valign="middle" id="credit">
                                                                        '.$footertext.'
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <!-- End Footer -->
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                </body>
            </html>';

        return $html;
    }

    /**
     * Hex darker/lighter/contrast functions for colours.
     *
     * @param  mixed  $color
     * @param  int  $factor  (default: 30)
     *
     * @return string
     */
    function hex_lighter($color, $factor = 30)
    {
        $base  = $this->rgb_from_hex($color);
        $color = '#';

        foreach ($base as $k => $v) {
            $amount      = 255 - $v;
            $amount      = $amount / 100;
            $amount      = round($amount * $factor);
            $new_decimal = $v + $amount;

            $new_hex_component = dechex($new_decimal);
            if (strlen($new_hex_component) < 2) {
                $new_hex_component = "0".$new_hex_component;
            }
            $color .= $new_hex_component;
        }

        return $color;
    }

    public function light_or_dark($color, $dark = '#000000', $light = '#FFFFFF')
    {

        $hex = str_replace('#', '', $color);

        $c_r = hexdec(substr($hex, 0, 2));
        $c_g = hexdec(substr($hex, 2, 2));
        $c_b = hexdec(substr($hex, 4, 2));

        $brightness = (($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000;

        return $brightness > 155 ? $dark : $light;
    }

    public function rgb_from_hex($hex)
    {
        /*$color = str_replace( '#', '', $color );
        // Convert shorthand colors to full format, e.g. "FFF" -> "FFFFFF"
        $color = preg_replace( '~^(.)(.)(.)$~', '$1$1$2$2$3$3', $color );

        $rgb = array();
        //            $rgb['R'] = hexdec($color{0} . $color{1});
        //            $rgb['G'] = hexdec($color{2} . $color{3});
        //            $rgb['B'] = hexdec($color{4} . $color{5});

        return $rgb;*/

        preg_match("/^#{0,1}([0-9a-f]{1,6})$/i", $hex, $match);
        if (!isset($match[1])) {
            return false;
        }

        if (strlen($match[1]) == 6) {
            list($r, $g, $b) = array($hex[0].$hex[1], $hex[2].$hex[3], $hex[4].$hex[5]);
        } elseif (strlen($match[1]) == 3) {
            list($r, $g, $b) = array($hex[0].$hex[0], $hex[1].$hex[1], $hex[2].$hex[2]);
        } else {
            if (strlen($match[1]) == 2) {
                list($r, $g, $b) = array($hex[0].$hex[1], $hex[0].$hex[1], $hex[0].$hex[1]);
            } else {
                if (strlen($match[1]) == 1) {
                    list($r, $g, $b) = array($hex.$hex, $hex.$hex, $hex.$hex);
                } else {
                    return false;
                }
            }
        }

        $color      = array();
        $color['R'] = hexdec($r);
        $color['G'] = hexdec($g);
        $color['B'] = hexdec($b);

        return $color;
    }


    function hex_darker($color, $factor = 30)
    {
        $base  = $this->rgb_from_hex($color);
        $color = '#';

        foreach ($base as $k => $v) {
            $amount      = $v / 100;
            $amount      = round($amount * $factor);
            $new_decimal = $v - $amount;

            $new_hex_component = dechex($new_decimal);
            if (strlen($new_hex_component) < 2) {
                $new_hex_component = "0".$new_hex_component;
            }
            $color .= $new_hex_component;
        }

        return $color;
    }

    /* Convert hexdec color string to rgb(a) string */
    function hex2rgba($color, $opacity = false)
    {

        $default = 'rgb(0,0,0)';

        //Return default if no color provided
        if (empty($color)) {
            return $default;
        }

        //Sanitize $color if "#" is provided
        if ($color[0] == '#') {
            $color = substr($color, 1);
        }

        //Check if color has 6 or 3 characters and get values
        if (strlen($color) == 6) {
            $hex = array($color[0].$color[1], $color[2].$color[3], $color[4].$color[5]);
        } elseif (strlen($color) == 3) {
            $hex = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
        } else {
            return $default;
        }

        //Convert hexadec to rgb
        $rgb = array_map('hexdec', $hex);

        //Check if opacity is set(rgba or rgb)
        if ($opacity) {
            if (abs($opacity) > 1) {
                $opacity = 1.0;
            }
            $output = 'rgba('.implode(",", $rgb).','.$opacity.')';
        } else {
            $output = 'rgb('.implode(",", $rgb).')';
        }

        //Return rgb(a) color string
        return $output;
    }

    /**
     * Gete email style, it will be converted to inline later
     *
     * @return string
     * @throws Exception
     */
    public function emailGetStyle()
    {
        $direction   = 'ltr';
        $sitename    = get_bloginfo('name');
        $headerimage = $this->settings_api->get_option('headerimage', 'cbxpoll_email_setting', '');

        // Load colours
        $bg   = $this->settings_api->get_option('backgroundcolor', 'cbxpoll_email_setting', '#f5f5f5');
        $body = $this->settings_api->get_option('bodybackgroundcolor', 'cbxpoll_email_setting', '#fdfdfd');
        $base = $this->settings_api->get_option('basecolor', 'cbxpoll_email_setting', '#557da1');

        //$base_text = $this->settings_api->get_option('bodytextcolor','cbxpoll_email_setting', '#fdfdfd' );
        $base_text = $this->light_or_dark($base, '#202020', '#ffffff');
        //$text      = $base_text;
        $text = $this->settings_api->get_option('bodytextcolor', 'cbxpoll_email_setting', '#505050');

        $bg_darker_10    = $this->hex_darker($bg, 10);
        $body_darker_10  = $this->hex_darker($body, 10);
        $base_lighter_20 = $this->hex_lighter($base, 20);
        $base_lighter_40 = $this->hex_lighter($base, 40);
        $text_lighter_20 = $this->hex_lighter($text, 20);

        $right_or_left = ($direction == 'ltr') ? 'left' : 'right';

        // !important; is a gmail hack to prevent styles being stripped if it doesn't like something.
        $html = '
                #wrapper {
                    background-color: '.esc_attr($bg).';
                    margin: 0;
                    padding: 70px 0 70px 0;
                    -webkit-text-size-adjust: none !important;
                    width: 100%;
                }

                 #template_container {
                    box-shadow: 0 1px 4px rgba(0,0,0,0.1) !important;
                    background-color: '.$body.';
                    border: 1px solid '.$this->hex2rgba($bg_darker_10, .1).';
                    border-radius: 3px !important;
                    width:600px;
                }
                
                #template_body{
                    width:600px;
                }

                #template_header {
                    background-color: '.esc_attr($base).';
                    border-radius: 3px 3px 0 0 !important;
                    color: '.esc_attr($base_text).';
                    border-bottom: 0;
                    font-weight: bold;
                    line-height: 100%;
                    vertical-align: middle;
                    font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
                    width:600px;
                }
                
                #template_footer{
                    width: 600px;
                }

                #template_header h1 {
                    color: '.esc_attr($base_text).';
                }

                #template_footer td {
                    padding: 0;
                    -webkit-border-radius: 6px;
                }

                #template_footer #credit {
                    border:0;
                    color: '.esc_attr($base_lighter_40).';
                    font-family: Arial;
                    font-size:12px;
                    line-height:125%;
                    text-align:center;
                    padding: 0 48px 48px 48px;
                }

                #body_content {
                    background-color: '.esc_attr($body).';
                }

                #body_content table td {
                    padding: 48px;
                }

                #body_content table td td {
                    padding: 12px;
                }

                #body_content table td th {
                    padding: 12px;
                }

                #body_content p {
                    margin: 0 0 16px;
                }

                #body_content_inner {
                    color: '.esc_attr($text_lighter_20).';
                    font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
                    font-size: 14px;
                    line-height: 150%;
                    text-align: \''.$right_or_left.'\';
                }

                .td {
                    color: '.$text_lighter_20.';
                    border: 1px solid '.esc_attr($body_darker_10).';
                }

                .text {
                    color: '.esc_attr($text).';
                    font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
                }

                .link {
                    color: '.esc_attr($base).';
                }

                #header_wrapper {
                    padding: 36px 48px;
                    display: block;
                }

                h1 {
                    color:'.esc_attr($base).';
                    font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
                    font-size: 30px;
                    font-weight: 300;
                    line-height: 150%;
                    margin: 0;
                    text-align: \''.$right_or_left.'\';
                    text-shadow: 0 1px 0 '.esc_attr($base_lighter_20).';
                    -webkit-font-smoothing: antialiased;
                }

                h2 {
                    color: '.esc_attr($base).';
                    display: block;
                    font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
                    font-size: 18px;
                    font-weight: bold;
                    line-height: 130%;
                    margin: 16px 0 8px;
                    text-align: \''.$right_or_left.'\';
                }

                h3 {
                    color: '.esc_attr($base).';
                    display: block;
                    font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
                    font-size: 16px;
                    font-weight: bold;
                    line-height: 130%;
                    margin: 16px 0 8px;
                    text-align: \''.$right_or_left.'\';
                }

                a {
                    color: '.esc_attr($base).';
                    font-weight: normal;
                    text-decoration: underline;
                }

                img {
                    border: none;
                    display: inline;
                    font-size: 14px;
                    font-weight: bold;
                    height: auto;
                    line-height: 100%;
                    outline: none;
                    text-decoration: none;
                    text-transform: capitalize;
                }
                
                @media only screen and (max-width: 620px) {
                    #template_container {
                        width: 98% !important;
                    }
                    
                    #template_body {
                        width: 98% !important;
                    }
                    
                    #template_header {
                        width: 98% !important;
                    }
                    
                    #template_footer {
                        width: 98% !important;
                    }
                    
                     #header_wrapper {
	                    padding: 20px 10px !important;
	                    display: block;
	                }
                    
                    h1 {
	                    
	                    font-size: 20px  !important;
	                    font-weight: bold  !important;
	                    line-height: 120%  !important;
	                    
	                }
	
	                h2 {
	                    
	                    font-size: 18px  !important;
	                    font-weight: bold  !important;
	                    line-height: 110%  !important;
	                   
	                }
	
	                h3 {
	                    
	                    font-size: 16px  !important;
	                    font-weight: bold  !important;
	                    line-height: 110%  !important;
	                    
	                }
                }
                ';

        return $html;

    }


    /**
     * General email template
     *
     * @return string
     */
    public function getHtmlTemplate()
    {
        return $this->emailGetHeader().'{mainbody}'.$this->emailGetFooter();
    }

    public function htmlEmeilify($html)
    {
        $css = $this->emailGetStyle(); //get the css and now need to convert it to inline

        try {

            // apply CSS styles inline for picky email clients
            $emogrifier = new CBXPollEmogrifier($html, $css);
            $html       = $emogrifier->emogrify();

        } catch (Exception $e) {

            //$logger = new WC_Logger();
            //$logger->add( 'emogrifier', $e->getMessage() );
        }

        return $html;
    }
}//end class CBXPollEmailTemplate
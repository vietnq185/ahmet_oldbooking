<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

    <style type="text/css">
        <?php echo file_get_contents(PJ_INSTALL_URL . PJ_CSS_PATH . 'emails.css'); ?>
    </style>
</head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
    <main class="main" role="main">
        <header class="site-title color">
            <div class="wrap" style="margin: 0 auto; width: 1170px; text-align: left; position: relative; max-width: 94%; display: inline-block;">
                <div class="container" style="height: 100px; display: table; color: #fff; width: 100%;">
                    <h1><?php __('front_step_booking_summary'); ?></h1>
                </div>
            </div>
        </header>

        <div class="wrap">
            <div class="row" style="margin: 0 -15px;">
                <div class="full-width" style="float: left; width: 100%; padding: 0 15px 30px; max-width: 100%;">
                    <div class="box readonly" style="float: left; width: 100%; background: #fff; padding: 20px 25px 25px; color: #34394A; -webkit-box-shadow: inset 0 0 0 1px #DFDFD0; -moz-box-shadow: inset 0 0 0 1px #DFDFD0; box-shadow: inset 0 0 0 1px #DFDFD0; margin: 0 0 30px; -webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; position: relative;">
                        {EMAIL_BODY}
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer black" role="contentinfo">
        <div class="wrap" style="margin: 0 auto; width: 1170px; text-align: left; position: relative; max-width: 94%; display: inline-block;">
            <div class="row" style="margin: 0 -15px;">
                <!-- Column -->
                <article class="one-half" style="width: 50%;">
                    <h6><?php __('front_about_us'); ?></h6>
                    <p><?php __('front_about_us_text'); ?></p>
                </article>
                <!-- //Column -->

                <!-- Column -->
                <article class="one-fourth" style="width: 25%;">
                    <h6><?php __('front_need_help'); ?></h6>
                    <p><?php __('front_need_help_text'); ?></p>
                    <p class="contact-data" style="font-size: 17px; position: relative; padding: 10px 0 10px 35px; white-space: nowrap; font-family: 'Montserrat', sans-serif;"><span class="icon icon-themeenergy_call"></span> <?php __('front_need_help_phone'); ?></p>
                    <p class="contact-data" style="font-size: 17px; position: relative; padding: 10px 0 10px 35px; white-space: nowrap; font-family: 'Montserrat', sans-serif;"><span class="icon icon-themeenergy_mail-2"></span> <a href="mailto:<?= __('front_need_help_email', true, false) ?>"><?php __('front_need_help_email'); ?></a></p>
                </article>
                <!-- //Column -->

                <!-- Column -->
                <article class="one-fourth" style="width: 25%;">
                    <h6><?php __('front_follow_us'); ?></h6>
                    <ul class="social">
                        <li><a href="https://www.facebook.com/" title="Facebook"><i class="fa fa-fw fa-facebook"></i></a></li>
                        <li><a href="https://twitter.com/" title="Twitter"><i class="fa fa-fw fa-twitter"></i></a></li>
                        <li><a href="https://plus.google.com/" title="Google Plus"><i class="fa fa-fw fa-google-plus"></i></a></li>
                        <li><a href="https://www.linkedin.com/" title="LinkedIn"><i class="fa fa-fw fa-linkedin"></i></a></li>
                        <li><a href="https://www.pinterest.com/" title="Pinterest"><i class="fa fa-fw fa-pinterest-p"></i></a></li>
                        <li><a href="https://vimeo.com/" title="Vimeo"><i class="fa fa-fw fa-vimeo"></i></a></li>
                    </ul>
                </article>
                <!-- //Column -->
            </div>

            <div class="copy" style="float: left; width: 100%; color: #727679; padding: 20px 0 0; border-top: 1px solid rgba(255,255,255,.07); -webkit-box-shadow: 0 -1px 0 rgba(0,0,0,.5); -moz-box-shadow: 0 -1px 0 rgba(0,0,0,.5); box-shadow: 0 -1px 0 rgba(0,0,0,.5);">
                <p style="float: left; max-width: 40%;"><?php __('front_copyright'); ?></p>

                <nav role="navigation" class="foot-nav">
                    <ul>
                        <li><a href="#" title="<?php __('front_home'); ?>"><?php __('front_home'); ?></a></li>
                        <li><a href="#" title="<?php __('front_blog'); ?>"><?php __('front_blog'); ?></a></li>
                        <li><a href="#" title="<?php __('front_about_us'); ?>"><?php __('front_about_us'); ?></a></li>
                        <li><a href="#" title="<?php __('front_contact_us'); ?>"><?php __('front_contact_us'); ?></a></li>
                        <li><a href="#" title="<?php __('front_terms_of_use'); ?>"><?php __('front_terms_of_use'); ?></a></li>
                        <li><a href="#" title="<?php __('front_help'); ?>"><?php __('front_help'); ?></a></li>
                        <li><a href="#" title="<?php __('front_for_partners'); ?>"><?php __('front_for_partners'); ?></a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </footer>
</body>
</html>
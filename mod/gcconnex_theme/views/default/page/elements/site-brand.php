<?php 
/**
 * WET 4 Site Branding
 *
 */

// footer
//echo elgg_view('core/account/login_dropdown');
$site_url = elgg_get_site_url();

// cyu - strip off the "GCconnex" branding bar for the gsa
if (elgg_is_active_plugin('gc_fedsearch_gsa') && ((!$gsa_usertest) && strcmp($gsa_agentstring,strtolower($_SERVER['HTTP_USER_AGENT'])) == 0) || strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'gsa-crawler') !== false ) {

} else {
?>


    <div id="app-brand">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 ">
                    <div class="app-name pull-left">
                    <a href="<?php echo $site_url; ?>">
                        <span><span class="bold-gc">GC</span>connex</span>
                    </a>
                    </div>
                    
                    <div class="app-dropdown-holder hidden-xs pull-left">
                    <div class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Dropdown Example
                            <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li>
                                   <a href="<?php echo elgg_echo('wet:gcpediaLink');?>">
                                        <img class="tool-link-icon" src="<?php echo $site_url.'/mod/wet4/graphics/pedia_icon.png';?>" alt="GCpedia" /><span class="bold-gc">GC</span><?php echo elgg_echo('wet:barGCpedia');?>
                                    </a>    
                                </li>
                                <li>
                                   <a href="<?php echo elgg_echo('wet:gcintranetLink-toolsHead');?>">
                                        <img class="tool-link-icon" src="<?php echo $site_url.'/mod/wet4/graphics/intranet_icon.png'?>" alt="GCintranet"/><span class="bold-gc">GC</span>intranet
                                    </a>  
                                </li>
                                <li>
                                    <a href="<?php echo elgg_echo('wet:gcdirectoryLink');?>">
                                        <img class="tool-link-icon" src="<?php echo $site_url.'/mod/wet4/graphics/directory_icon.png'?>" alt="GCDirectory" /><span class="bold-gc">GC</span><?php echo elgg_echo('wet:barDirectory');?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo elgg_echo('wet:gccollabLink');?>">
                                        <img class="tool-link-icon" src="<?php echo $site_url.'/mod/wet4/graphics/collab_icon.png'?>" alt="GCcollab" /><span class="bold-gc">GC</span>collab
                                    </a>
                                </li>

                            </ul>
                    </div>

                </div>
                </div>
                

                
                
                                <section class="wb-mb-links col-xs-4 col-sm-6 visible-sm visible-xs" id="wb-glb-mn">
                <h2><?php echo elgg_echo('wet:search');?></h2>
                <ul class="list-inline text-right chvrn">
                <li><a href="#mb-pnl" title="<?php echo elgg_echo('wet:search');?>" aria-controls="mb-pnl" class="overlay-lnk" role="button">
                        <span class="glyphicon glyphicon-search">
                            <span class="glyphicon glyphicon-th-list">
                            <span class="wb-inv">
                                <?php echo elgg_echo('wet:search');?>
                            </span>
                            </span>
                        </span>
                        </a>
                    </li>
                </ul>
                <div id="mb-pnl"></div>
                </section>

            <?php echo elgg_view('search/search_box', $vars); ?>
            </div>
        </div>

    </div>

<?php } ?>
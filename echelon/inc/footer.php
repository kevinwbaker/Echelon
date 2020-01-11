<?php 	
if($pagination && !$db->error) : // check to see if pagination is required on this page
	if(!$no_data) : // if there no recorded records ?>
    
        <h6 class="text-center my-4">
            <?php recordNumber($start_row, $limit_rows, $total_rows); ?>
        </h6>
        
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
        
			<?php if($total_rows > $limit_rows) : /* If the number of rows returned is not more than the min per page then don't show this section */ ?>
								
                    <li class="page-item<?php if($page_no == 0)  {echo ' disabled';} ?>"><a href="<?php printf("%25s?p=%d%s", $this_page, 0, $query_string_page); ?>" class="page-link" title="Go to the first page">&laquo; First</a></li>
        
                    <li class="page-item<?php if($page_no == 0)  {echo ' disabled';} ?>"><a href="<?php printf("%25s?p=%d%s", $this_page, max(0, $page_no - 1), $query_string_page); ?>" class="page-link" title="Go to the previous page">&lsaquo; Previous</a></li>
                    
                    <?php if($page_no - 1 > 0) { ?>
                        <li class="page-item"><a href="<?php printf("%25s?p=%d%s", $this_page, max(0, $page_no - 2), $query_string_page); ?>" class="page-link"><?php echo $page_no - 1; ?></a></li>
                    <?php } ?>
                
                    <?php if($page_no > 0) { ?>
                        <li class="page-item"><a href="<?php printf("%25s?p=%d%s", $this_page, max(0, $page_no - 1), $query_string_page); ?>" class="page-link"><?php echo $page_no; ?></a></li>
                    <?php } ?>
					
					<li class="page-item active"><a class="page-link"><?php echo $page_no + 1; ?></a></li>

                    <?php if($page_no + 2 < $total_pages) { ?>								
                        <li class="page-item"><a href="<?php printf("%25s?p=%d%s", $this_page, max(0, $page_no + 1), $query_string_page); ?>" class="page-link"><?php echo $page_no + 2; ?></a></li>
                    <?php } ?>
                    
                    <?php if($page_no + 3 < $total_pages) { ?>
                        <li class="page-item"><a href="<?php printf("%25s?p=%d%s", $this_page, max(0, $page_no + 2), $query_string_page); ?>" class="page-link"><?php echo $page_no + 3; ?></a></li>
                    <?php }?>
                    
                    <li class="page-item<?php if($page_no == $total_pages)  {echo ' disabled';} ?>"><a href="<?php printf("%25s?p=%d%s", $this_page, min($total_pages, $page_no + 1), $query_string_page); ?>" class="page-link" title="Go to the next page">Next &rsaquo;</a></li>
        
                    <li class="page-item<?php if($page_no == $total_pages)  {echo ' disabled';} ?>"><a href="<?php printf("%25s?p=%d%s", $this_page, $total_pages, $query_string_page); ?>" class="page-link" title="Go to the last page">Last &raquo;</a></li>   

				</ul>
			<?php endif; ?>
            </nav>
	<?php endif; // if there is data
endif; // end if pagination is on
?>

</div><!-- close #content -->
	    
    
</div> <!-- close #mc -->

<footer class="container sticky-bottom">
<hr>
<div class="row">
        <div class="col-md-6">
            <p>Developed by WatchMiltan, Eire32 (Kevin Baker) &amp; Big Brother Bot</p>
        </div>
        <div class="col-md-6">
            <p class="text-right">Echelon <small><?php echo ECH_VER; ?></p>
        </div>
<!--	
	<#?php if($mem->loggedIn()) { ?>
	
			<span class="foot-nav links">
				<a href="<?php echo $path; ?>" title="Home Page">Home</a> -
				<a href="<?php echo $path; ?>me.php" title="Edit your account">My Account</a> -
				<a href="<?php echo $path; ?>actions/logout.php" class="logout" title="Logout">Logout</a>
			</span>
		<#?php } ?>
        -->
	</div>
</footer><!-- close #footer -->



<!-- load jQuery off google CDN -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>

<!-- load main site js 
<script src="<#?php echo $path; ?>js/site.js" charset="utf-8"></script>
-->

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<!-- Popper JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>


<!-- page specific js -->
<?php if(isMe()) { ?>
	<script src="js/me.js" charset="utf-8"></script>
<?php } ?>

<?php if(isCD()) : ?>
	<script src="js/jquery.colorbox-min.js" charset="utf-8"></script>
	<script src="js/cd.js" charset="utf-8"></script>
	<script charset="utf-8">
		/*$('#level-pw').hide();

		// check for show/hide PW required for level change 
		if ($('#level').val() >= <?php echo $config['cosmos']['pw_req_level_group']; ?>) {
			$("#level-pw").show();
		}
		 $('#level').change(function(){  no exception for empty field, ergo password always has to be sent to server
			if ($('#level').val() >= 64) {
				$("#level-pw").slideDown();
			} else {
				$("#level-pw").slideUp();
			}
		}); */
	</script>
<?php endif; ?>

<?php
	## plugin specific js ##
	if(!$no_plugins_active)
		$plugins->getJS();
?>

</body>
</html>
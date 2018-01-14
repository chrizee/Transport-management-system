
	<div class="fh5co-cta" style="background-image: url(images/slide_4.jpg);">
		<div class="overlay"></div>
		<div class="container">
			<div class="col-md-12 text-center animate-box" data-animate-effect="fadeIn">
				<h3>We Try To Update The Site Everyday</h3>
				<p><a href="#" class="btn btn-primary btn-outline with-arrow">Get started now! <i class="icon-arrow-right"></i></a></p>
			</div>
		</div>
	</div>

	
	<footer id="fh5co-footer" role="contentinfo">
	
		<div class="container">
			<div class="col-md-3 col-sm-12 col-sm-push-0 col-xs-12 col-xs-push-0">
				<h3>About Us</h3>
				<p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts. </p>
				<p><a href="#" class="btn btn-primary btn-outline with-arrow btn-sm">I'm button <i class="icon-arrow-right"></i></a></p>
			</div>
			<div class="col-md-6 col-md-push-1 col-sm-12 col-sm-push-0 col-xs-12 col-xs-push-0">
				<h3>Our Services</h3>
				<ul class="float">
					<li><a href="#">Web Design</a></li>
					<li><a href="#">Branding &amp; Identity</a></li>
					<li><a href="#">Free HTML5</a></li>
					<li><a href="#">HandCrafted Templates</a></li>
				</ul>
				<ul class="float">
					<li><a href="#">Free Bootstrap Template</a></li>
					<li><a href="#">Free HTML5 Template</a></li>
					<li><a href="#">Free HTML5 &amp; CSS3 Template</a></li>
					<li><a href="#">HandCrafted Templates</a></li>
				</ul>

			</div>

			<div class="col-md-2 col-md-push-1 col-sm-12 col-sm-push-0 col-xs-12 col-xs-push-0">
				<h3>Follow Us</h3>
				<ul class="fh5co-social">
					<li><a href="#"><i class="icon-twitter"></i></a></li>
					<li><a href="#"><i class="icon-facebook"></i></a></li>
					<li><a href="#"><i class="icon-google-plus"></i></a></li>
					<li><a href="#"><i class="icon-instagram"></i></a></li>
				</ul>
			</div>
			
			
			<div class="col-md-12 fh5co-copyright text-center">
				<p>&copy; 2016 Free HTML5 template. All Rights Reserved. <span>Designed with <i class="icon-heart"></i> by <a href="http://freehtml5.co/" target="_blank">FreeHTML5.co</a> Demo Images by <a href="http://unsplash.com/" target="_blank">Unsplash</a></span></p>	
			</div>			
		</div>
	</footer>
</div>
	
	
	<!-- jQuery -->
	<script src="scripts/jquery.min.js"></script>
	<!-- jQuery Easing -->
	<script src="scripts/jquery.easing.1.3.js"></script>
	<!-- Bootstrap -->
	<script src="scripts/bootstrap.min.js"></script>
	<!-- Waypoints -->
	<script src="scripts/jquery.waypoints.min.js"></script>
	<!-- Flexslider -->
	<script src="scripts/jquery.flexslider-min.js"></script>

	<!-- MAIN scripts -->
	<script src="scripts/main.js"></script>
	<script type="text/javascript">
		$(document).ready(function () {
			$('img').addClass('img-responsive');

			$(".header-inner nav li a[href^='#']").on('click', function(e) {

	        // This sets the hash
		        var target;
		        target = this.hash;

		        // Prevent default anchor click behavior
		        e.preventDefault();

		        // The grabs the height of my header
		        var navOffset;
		        navOffset = $('#fh5co-header').height();

		        // Animate The Scroll
		        $('html, body').animate({
		            scrollTop: $(this.hash).offset().top - navOffset
		        }, 1000, function(){

		        // Adds hash to end of URL
		        return window.history.pushState(null, null, target);

	        	});

			});

			$('form#contact').on('submit', function(e) {
    	
		    	$(this).find('input[type="text"], input[type="email"], textarea').each(function(){
		    		if( $(this).val() == "" ) {
		    			e.preventDefault();
		    			$(this).addClass('input-error');
		    		}
		    		else {
		    			$(this).removeClass('input-error');
		    		}
		    	});
		    	
		    });
		});
	</script>
	
	<?php
		if(basename($_SERVER['PHP_SELF'], '.php') == 'test') {
	?>
        <script src="scripts/jquery.backstretch.min.js"></script>
        <script src="scripts/scripts.js"></script>

    <?php } ?>
	</body>
</html>


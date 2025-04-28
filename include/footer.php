<?php
// include/footer.php
// This file contains the closing HTML structure and the site footer content.
// It should be included at the end of every user-facing PHP page.
?>

    <?php // The main content of each page should be placed before this include ?>

    <footer id="main-footer">
        <?php // Social media links - consider using SVGs or icon fonts for better scaling ?>
        <a href="https://github.com/Ginshir0" target="_blank" rel="noopener noreferrer" title="GitHub Profile">
        <svg class="footer-icon" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
            viewBox="0 0 48 48" style="enable-background:new 0 0 48 48;" xml:space="preserve">
        <g id="github">
            <circle style="fill:none;stroke:var(--accent-color);stroke-width:3;stroke-miterlimit:10;" cx="24" cy="24" r="18.5"/>
            <path d="M17.784,34.156c-1.297,0-1.801-0.526-2.502-1.415c-0.692-0.889-1.437-1.488-2.331-1.736
                c-0.482-0.051-0.806,0.316-0.386,0.641c1.419,0.966,1.516,2.548,2.085,3.583C15.168,36.161,16.229,37,17.429,37H21v-2.844
                C20.098,34.156,18.686,34.156,17.784,34.156z"/>
            <path d="M24.08,30.216h-0.16c-2.717,0-4.92,2.203-4.92,4.92v7.806h10v-7.806C29,32.419,26.797,30.216,24.08,30.216z"/>
            <ellipse cx="24" cy="23" rx="10" ry="8"/>
            <path d="M32.2,13.077c-3.28,0-4.92,3.28-4.92,3.28l4.746,3.451C32.026,19.808,33.938,15.859,32.2,13.077z"/>
            <path d="M15.8,13.077c3.28,0,4.92,3.28,4.92,3.28l-4.746,2.631C15.974,18.988,14.062,15.859,15.8,13.077z"/>
        </g>

        </svg>
        </a>
        <a href="https://www.linkedin.com/in/jose-veras-b1a089ab/" target="_blank" rel="noopener noreferrer" title="LinkedIn Profile">
            <svg class="footer-icon" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	        viewBox="0 0 48 48" style="enable-background:new 0 0 48 48;" xml:space="preserve">
                <path style="fill:none;stroke:var(--accent-color);stroke-width:3;stroke-miterlimit:10;" d="M36.5,40.5h-25c-2.209,0-4-1.791-4-4v-25
	            c0-2.209,1.791-4,4-4h25c2.209,0,4,1.791,4,4v25C40.5,38.709,38.709,40.5,36.5,40.5z"/>
                <circle cx="15.5" cy="15.5" r="2.5"/>
                <path d="M17,35h-3c-0.553,0-1-0.447-1-1V21c0-0.553,0.447-1,1-1h3c0.553,0,1,0.447,1,1v13C18,34.553,17.553,35,17,35z"/>
                <path d="M29,20c-1.538,0-2.937,0.586-4,1.541V21c0-0.553-0.447-1-1-1h-3c-0.553,0-1,0.447-1,1v13c0,0.553,0.447,1,1,1h3
	            c0.553,0,1-0.447,1-1v-7.5c0-1.379,1.121-2.5,2.5-2.5s2.5,1.121,2.5,2.5V34c0,0.553,0.447,1,1,1h3c0.553,0,1-0.447,1-1v-8
	            C35,22.691,32.309,20,29,20z"/>
            </svg>
        </a>
        <a href="https://twitter.com/josever65725881" target="_blank" rel="noopener noreferrer" title="Twitter Profile">
        <svg class="footer-icon" version="1.1" baseProfile="basic" id="Layer_1"
	    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 48 48"
	     xml:space="preserve">
            <path style="fill:none;stroke:var(--accent-color);stroke-width:3;stroke-miterlimit:10;" d="M35.5,40.5h-23c-2.761,0-5-2.239-5-5v-23
	        c0-2.761,2.239-5,5-5h23c2.761,0,5,2.239,5,5v23C40.5,38.261,38.261,40.5,35.5,40.5z"/>
            <g>
	            <path d="M34.257,34h-6.437L13.829,14h6.437L34.257,34z M28.587,32.304h2.563L19.499,15.696h-2.563L28.587,32.304z"/>
	            <polygon points="15.866,34 23.069,25.656 22.127,24.407 13.823,34 	"/>
	            <polygon points="24.45,21.721 25.355,23.01 33.136,14 31.136,14 	"/>
            </g>
        </svg>
        </a>

        <?php // Copyright notice with dynamic year ?>
        <p style="margin-top: 15px;">&copy; <?php echo date("Y"); ?> Jose Veras. All rights reserved.</p>
    </footer>

    <?php // Closing the body and html tags that were opened in header.php ?>
</body>
</html>

<!DOCTYPE html>
<html dir="ltr" lang="nl">
<head>

	<meta http-equiv="content-type" content="text/html; charset=utf-8" />

	<!-- Stylesheets
	============================================= -->
	<meta name="viewport" content="width=device-width, initial-scale=1" />

	<!-- Document Title
	============================================= -->
	<title>Plasty | Hollywood</title>

</head>

<body class="stretched">

<!-- Document Wrapper
============================================= -->
<div id="wrapper" class="clearfix">

  <!-- Content
  ============================================= -->
  <section id="content">    
    <form id="login-form" name="login-form" class="nobottommargin" action="admin.php?action=login" method="post">
      <input type="hidden" name="login" value="true" />

    <?php if ( isset( $results['errorMessage'] ) ) { ?>
      <div class="errorMessage"><?php echo $results['errorMessage'] ?></div>
    <?php } ?>
      
      <h3 style="text-align: center;">CMS Login</h3>

      <div class="col_full">
        <label for="login-form-username">Username:</label>
        <input type="text" id="username" name="username" class="form-control not-dark" />
      </div>

      <div class="col_full">
        <label for="login-form-password">Password:</label>
        <input type="password" id="password" name="password" class="form-control not-dark" />
      </div>

      <div class="col_full nobottommargin">
        <button class="button button-3d button-black nomargin" id="login-form-submit" name="login" value="Login" type="submit">Login</button>
      </div>
    </form>

  </section><!-- #content end -->

</div><!-- #wrapper end -->

</body>
</html>
<?php
require_once("meredith_db.class.php");
session_start();
if (!array_key_exists("uname", $_SESSION)) {
	header('Location: login.php');
	exit;
}

$sellerInfo = mysqli_fetch_array( SellerDB::getInstance()->get_seller_info( $uname ) );

include('head.inc.php');
?>

<header>
	<div>
		<h1>MSG Sales Call Tracker</h1>
	</div>
	<div>
	  <a href="/"><img src="/images/msg-logo.png" alt="Meredith Sales Guarantee" /></a>
	</div>
	<nav>
	  <ul>
	    <li><a href="toolbox.php">Toolbox Home</a></li>
	    <?php
	    	include('nav.inc.php');
	    ?>
	  </ul>
	</nav>
</header>

<div class="form-bg">
	<div>
		<h1><?php echo $sellerInfo['uname']; ?></h1>
		<p>
			Total Points: <?php echo $sellerInfo['total_points']; ?><br />
			Group Leader: <?php echo $sellerInfo['group']; ?>
		</p>
	</div>
	<div class="form-row hd">
		<div>Date</div>
		<div>Mtg Date</div>
		<div>Agency Name</div>
		<div>Agency Attendee(s) Names</div>
		<div>Client Name</div>
		<div>Client Attendee(s) Names</div>
		<div>Michael/Britta Presented?</div>
		<div class="small center">Points</div>
		<div class="last">&nbsp;</div>
	</div>
	<div class="form-row">
		<form class="save-mtg" action="saleslog-update.php" method="post">
			<div class="small">
				<?php
					$today = date("m/d/Y");
					echo $today;
				?>
				<input type="hidden" name="date" value="<?php echo $today; ?>" />
			</div>
			<div><input name="mdate" id="mdate" class="date-pick" value="" />	</div>
			<div><input type="text" name="agency" id="agency" /></div>
			<div><input type="text" name="a_attendees" id="a_attendees" /></div>
			<div><input type="text" name="client" id="client" /></div>
			<div><input type="text" name="c_attendees" id="c_attendees" /></div>
			<div>
				<select name="michael_britta" id="team">
					<option value="No">No</option>
					<option value="Yes">Yes</option>
				</select>					
			</div>
			<div  class="small center"><input type="submit" value="SAVE" class="btn-save" /></div>
			<input type="hidden" name="uid" value="<?php echo $sellerInfo['uid'];  ?>" />
			<input type="hidden" name="uname" value="<?php echo $sellerInfo['uname'];  ?>" />
			<input type="hidden" name="action" value="save" />
		</form>
	</div>
	<?php
	$meetings = SellerDB::getInstance()->get_meetings_by_seller( $sellerInfo['uid'], $_SESSION['numrecords'] );

	while ($row = mysqli_fetch_array($meetings)) {
		echo '<div class="form-row">';
		echo '<div class="small">'.$row['date'].'</div>';
		echo '<div>'.$row['mdate'].'</div>';
		echo '<div>'.$row['agency'].'</div>';
		echo '<div>'.$row['a_attendees'].'</div>';
		echo '<div>'.$row['client'].'</div>';
		echo '<div>'.$row['c_attendees'].'</div>';
		echo '<div>'.$row['michael_britta'].'</div>';
		echo '<div class="small center">'.$row['points'].'</div>';
		$msg = "'Are you sure you want to delete this record?'";
		echo '<div class="small center"><form name="update" onsubmit="return confirm('.$msg.');" method="post" action="update.php">';
			echo '<button type="submit" class="btn-edit"></button><button type="submit" class="btn-ical"></button><button type="submit" class="btn-share"></button>';
			echo '<input type="hidden" name="rec_num" value="'.$row['rec_num'].'" />';
			echo '<input type="hidden" name="points" value="'.$row['points'].'" />';
			echo '<input type="hidden" name="uid" value="'.$sellerInfo['uid'].'" />';
			echo '<input type="hidden" name="action" value="delete" />';
		echo '</form></div>';
		echo '</div>';
	}
	?>
	<div class="form-row">
		<form action="saleslog-seller.php" method="post" name="more" class="more">
			<input type="submit" value="See More Records" />
			<input type="hidden" name="read_more" value="1" />
			<?php
			if (array_key_exists("managerLookup", $_POST)) {
				echo '<input type="hidden" name="mngrlookup" value="true" />';
				echo '<input type="hidden" name="uname" value="'.$uname.'" />';
			}
			?>
		</form>
	</div>
</div>

<div class="topten">
	<h2>Top 10 Sellers</h2>
	<ol>
		<?php
		$topten = SellerDB::getInstance()->get_top_ten();

		while ($row = mysqli_fetch_array($topten)) {
			echo '<li>'.$row['uname'].', '.$row['department'].', '.$row['total_points'].'</li>';
		}
		?>
	</ol>
</div>

<?php
include('footer.inc.php');
?>


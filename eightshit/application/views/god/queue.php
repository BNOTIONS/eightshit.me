<script src="/media/js/god-queue.js"></script>
<h2>QUEUE</h2>

<?php if(sizeof($queue) == 0): ?>

<h3>HA! Nothing Here</h3>

<?php else: ?>
<table>
	<thead>
		<tr>
			<th>ID</th>
			<th>Picture</th>
			<th>Author</th>
			<th>Accept</th>
			<th>Recipient</th>
			<th>Delete</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($queue as $pic):?>
		<tr>
			<td><?php echo $pic['id'] ?></td>
			<td><img src='/index.php/god/img_preview/<?php echo $pic['id'] ?>' /></td>
			<td><?php echo $pic['creator_name'] ?></td>
			<td><a href="#" class="accept" img_id="<?php echo $pic['id'] ?>">ACCEPT</a></td>
			<td><input id="recipient_<?php echo $pic['id'] ?>" /></td>
			<td><a href="#" class="deny" img_id="<?php echo $pic['id'] ?>">DENY</a></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
	</table>
<?php endif; ?>
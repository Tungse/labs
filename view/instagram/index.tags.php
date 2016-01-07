<?php foreach($instagrams as $id => $instagram) : ?>
<div class="instagram-holder">
	<div class="image-holder"><a href="<?php echo $instagram->url; ?>" target="_new"><?php if(isset($instagram->image)) : ?><img class="image" src="<?php echo $instagram->image; ?>" /><?php endif; ?></a></div>
	<div class="user">
		<table cellpadding="0" cellspacing="0" boder="0">
			<tr>
				<td class="td-user-image"><?php if(isset($instagram->userImage)) : ?><img class="user-image" src="<?php echo $instagram->userImage; ?>" /><?php endif; ?>	</td>
				<td class="td-user-info">
					<div class="user-info">
						<span class="user-name"><?php echo $this->cutString($instagram->userName); ?></span><br />
						<span class="instagram-datetime"><?php echo $instagram->datetime; ?></span>
					</div>
				</td>
			</tr>
		</table>
	</div>
</div>
<?php endforeach; ?>

<?php if(isset($pagination)) : ?><div class="button-next-url" url="<?php echo $pagination; ?>">&nbsp;</div><?php endif; ?>
<?php if(isset($error)) : ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
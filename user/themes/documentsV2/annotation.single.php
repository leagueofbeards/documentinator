<?php namespace Habari; ?>
<?php $theme->display('header'); ?>
<div class="row sixteen">
	<?php $theme->display('page.sidebar'); ?>
	<div class="thirteen columns omega offset-by-four">
		<div class="article page">
			<header><h1 contenteditable="false">Annotation on <?php echo $document->title; ?></h1></header>
			<hr class="large">
			<div class="doc-section body editable" id="intro" name="content" contenteditable="false" designmode="off">
				<p class="annotation">Made <?php echo DateTime::date_create($annotation->pubdate)->friendly(1); ?> by <?php echo $person->displayname; ?> on &ldquo;<span><?php echo $annotation->content; ?>&hellip;</span>&rdquo;</p>
				<p><blockquote><?php echo $annotation->text; ?></blockquote></p>
			</div>
			<hr class="small">
			<div class="comments">
				<h5><?php echo $theme->comments_count($annotation,'%d Responses','%d Response','%d Responses'); ?> <?php _e('to'); ?> Annotation on <?php echo $document->title; ?></h5>
				<ul id="commentlist">
				<?php
				if ( $annotation->comments->moderated->count ) {
					foreach ( $annotation->comments->comments->moderated as $comment ) {
					$class = 'class="comment';
					if ( $comment->status == Comment::STATUS_UNAPPROVED ) {
						$class.= '-unapproved';
					}
					$class.= '"';
				?>
					<li id="comment-<?php echo $comment->id; ?>" <?php echo $class; ?>>
		 				<div class="twelve alpha columns comment-content">
			 				<div class="one columns alpha">
				 				<?php Gravatar::show($comment->email); ?>
			 				</div>
			 				<div class="ten columns omega">
				 				<?php echo $comment->content_out; ?>
				 				<div class="comment-meta">
									<span class="commentauthor"><?php echo $comment->name; ?></span>
									<span class="commentdate">
										<a href="#comment-<?php echo $comment->id; ?>" title="<?php _e('Time of this comment'); ?>">
										<?php echo DateTime::date_create($comment->date )->friendly(1); ?></a></span>
										<h5><?php if ( $comment->status == Comment::STATUS_UNAPPROVED ) : ?> <em><?php _e('In moderation'); ?></em><?php endif; ?></h5>
								</div>
			 				</div>
				       </div>
				       <div class="clear"></div>
					</li>
			<?php
				}
			} else {
				_e('<li>There are currently no comments.</li>');
			}
			?>
			</ul>
			<hr>
			<?php $annotation->comment_form()->out(); ?>
			</div>
		</div>
	</div>
</div>
<script>
	DI.page = "<?php echo $annotation->permalink; ?>";
	DI.page_id = "<?php echo $annotation->id; ?>";
</script>
<?php $theme->display('footer'); ?>
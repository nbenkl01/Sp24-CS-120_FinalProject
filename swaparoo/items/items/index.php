<?php session_start(); include '../../functions.php'; shared_header('Items')?>
<?php $pdo = connect_mysql(); date_default_timezone_set('America/New_York'); ?>

<?php

// Check if the item parameter is specified in the URL
if (!isset($_GET['item'])) {
    exit('Item does not exist!');
}

$stmt = $pdo->prepare('SELECT * FROM Items WHERE item_id = ?');
$stmt->execute([$_GET['item']]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    exit('Item does not exist!');
}

// Query the Users table to get the current owner's username
$stmt = $pdo->prepare('SELECT username FROM Users WHERE user_id = ?');
$stmt->execute([$item['owner_id']]);
$owner = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$owner) {
    $currentOwner = 'Unknown';
} else {
    $currentOwner = $owner['username'];
}


// Query the Comments table to get comments associated with the item
$stmt = $pdo->prepare('SELECT * FROM Comments WHERE item_id = ? ORDER BY comment_timestamp DESC');
$stmt->execute([$_GET['item']]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php
// Below function will convert datetime to time elapsed string
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    $w = floor($diff->d / 7);
    $diff->d -= $w * 7;
    $string = ['y' => 'year','m' => 'month','w' => 'week','d' => 'day','h' => 'hour','i' => 'minute','s' => 'second'];
    foreach ($string as $k => &$v) {
        if ($k == 'w' && $w) {
            $v = $w . ' week' . ($w > 1 ? 's' : '');
        } else if (isset($diff->$k) && $diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }
    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

// This function will populate the comments and comments replies using a loop
function show_comments($comments, $parent_id = -1) {
    $html = '';
    if ($parent_id != -1) {
        // If the comments are replies sort them by the "comment_timestamp" column
        array_multisort(array_column($comments, 'comment_timestamp'), SORT_ASC, $comments);
    }
    // Iterate the comments using the foreach loop
    foreach ($comments as $comment) {
        if ($comment['parent_id'] == $parent_id) {
            // Add the comment to the $html variable
            $html .= '
            <div class="comment">
                <div>
                    <h3 class="name">' . htmlspecialchars($comment['username'], ENT_QUOTES) . '</h3>
                    <span class="date">' . time_elapsed_string($comment['comment_timestamp']) . '</span>
                </div>
                <p class="content">' . nl2br(htmlspecialchars($comment['comment_text'], ENT_QUOTES)) . '</p>
                <a class="reply_comment_btn" href="#" data-comment-id="' . $comment['comment_id'] . '">Reply</a>
                ' . show_write_comment_form($comment['comment_id']) . '
                <div class="replies">
                ' . show_comments($comments, $comment['comment_id']) . '
                </div>
            </div>
            ';
        }
    }
    return $html;
}

// This function is the template for the write comment form
function show_write_comment_form($parent_id = -1) {
    $html = '';
    if (isset($_SESSION['user_id'])) {
        $html .= '
        <div class="write_comment" data-comment-id="' . $parent_id . '">
            <form method="post" action="">
                <input name="item_id" type="hidden" value="' . $_GET['item'] . '">    
                <input name="parent_id" type="hidden" value="' . $parent_id . '">
                <input name="user_id" type="hidden" value="' . $_SESSION['user_id'] . '">
                <input name="username" type="text" value="' . $_SESSION['name'] . '" readonly>
                <textarea name="comment_text" placeholder="Write your comment here..." required></textarea>
                <button type="submit">Submit Comment</button>
                <button type="button" class="cancel_comment_btn">Cancel</button>
            </form>
        </div>';
    } else {
        $html .= '
        <div class="write_comment" data-comment-id="' . $parent_id . '">
            <form method="post" action="">
                <input name="username" type="text" value="Unknown User" readonly>
                <textarea name="comment_text" type = "text" placeholder="Please log in to comment" readonly></textarea>
                <button type="button" class="cancel_comment_btn">Cancel</button>
            </form>
        </div>';
    }
    return $html;
}

// Check if the submitted form variables exist
if (isset($_POST['user_id'], $_POST['comment_text'])) {
    // POST variables exist, insert a new comment into the MySQL comments table (user submitted form)
    $stmt = $pdo->prepare('INSERT INTO Comments (item_id, parent_id, user_id, comment_text, comment_timestamp) VALUES (?,?,?,?,NOW())');
    $stmt->execute([ $_GET['item'], $_POST['parent_id'], $_POST['user_id'], $_POST['comment_text'] ]);
    exit('Your comment has been submitted!');
}

// Query the Comments table to get comments associated with the item
$stmt = $pdo->prepare('SELECT c.*, u.username FROM Comments c JOIN Users u ON c.user_id = u.user_id WHERE item_id = ? ORDER BY comment_timestamp DESC');
$stmt->execute([$_GET['item']]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Get the total number of comments
$stmt = $pdo->prepare('SELECT COUNT(*) AS total_comments FROM Comments WHERE item_id = ?');
$stmt->execute([ $_GET['item'] ]);
$comments_info = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="item content-wrapper">
    <div class="overlay">
        <img class = "cover" src="/swaparoo/../images/items/<?=$item['item_id']?>.webp" alt="<?=$item['title']?>">
    </div>
    <div>
        <h1 class="name"><?=$item['title']?></h1>
        <h2 class="authortextpending" style="margin-top: 0px;"><?=$item['author']?></h2>
        <span class="creditcost" style="font-size: 2rem;">
            <i class="fas fa-coins"></i>
            <?=$item['credit_value']?>
        </span>    
        <form action="/swaparoo/cart/index.php" method="post">
            <input type="hidden" name="item_id" value="<?=$item['item_id']?>">
            <table>
                <tr>
                    <td><strong>Condition:</strong></td>
                    <td><?=$item['item_condition']?></td>
                </tr>    
                <tr>
                    <td><strong>Availability:</strong></td>
                    <td><?= $item['available'] ? 'Available' : 'Unavailable' ?></td>
                </tr>
                <tr>
                    <td><strong>Current Owner:</strong></td>
                    <td><?=$currentOwner?></td>
                </tr>
            </table>
            <input type="submit" value="Put in Pouch">
        </form>
    </div>
</div>

<div class = "content-wrapper"> 
    <div class="navigation-links">
        <a href="#item-description" class="internal-nav-link active">Item Description</a> <a href="#comments" class="internal-nav-link">Comments</a>  <a href="#history" class="internal-nav-link no-border">Item History</a>
    </div>

    <!-- Item Description -->
    <div class="content-section description" id="item-description">
        <?=$item['description']?>
    </div>

    <!-- Comments Section -->
    <div class="content-section comments" id="comments"  style="display: none;">
        <div class="comment_header">
            <span class="total"><?= $comments_info['total_comments'] ?> comments</span>
            <a href="#" class="write_comment_btn" data-comment-id="-1">Write Comment</a>
        </div>

        <!-- Write comment form -->
        <?= show_write_comment_form() ?>

        <!-- Display comments -->
        <?= show_comments($comments) ?>
    </div>

    <!-- Item History -->
    <div class="content-section history" id="history"  style="display: none;">
        <p>To-Do</p>
        <!-- Your comments section content here -->
    </div>
</div>

<?=shared_footer()?>

<script>
// JavaScript to handle navigation link clicks
document.addEventListener('DOMContentLoaded', function() {
    // Get all navigation links
    var navLinks = document.querySelectorAll('.internal-nav-link');
    var lastActiveTab = document.querySelector('.internal-nav-link.active');

    // Add mouseover event listener to each navigation link
    navLinks.forEach(function(navLink) {
        navLink.addEventListener('mouseover', function(event) {
            // Remove 'active' class from all navigation links
            navLinks.forEach(function(link) {
                link.classList.remove('active');
            });
        });
    });

    // Add mouseout event listener to revert to last active tab
    navLinks.forEach(function(navLink) {
        navLink.addEventListener('mouseout', function(event) {
            if (lastActiveTab) {
                lastActiveTab.classList.add('active');
            }
        });
    });

    // Add click event listener to each navigation link
    navLinks.forEach(function(navLink) {
        navLink.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent default link behavior
            
            // Remove 'active' class from all navigation links
            navLinks.forEach(function(link) {
                link.classList.remove('active');
            });
            
            // Add 'active' class to the clicked navigation link
            this.classList.add('active');
            
            // Store reference to the last active tab
            lastActiveTab = this;

            // Get the target section ID from the href attribute
            var targetId = this.getAttribute('href');
            
            // Hide all content sections
            var contentSections = document.querySelectorAll('.content-section');
            contentSections.forEach(function(section) {
                section.style.display = 'none';
            });
            
            // Show the target content section
            document.querySelector(targetId).style.display = 'block';
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Event listener for "Write Comment" button click
    document.querySelector('.write_comment_btn').addEventListener('click', function(event) {
        event.preventDefault();
        document.querySelector('.write_comment').style.display = 'block'; // Show the comment form
    });

    // Event listener for "Reply" button click
    document.querySelectorAll('.reply_comment_btn').forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const commentId = this.getAttribute('data-comment-id');
            const replyForm = document.querySelector('.write_comment[data-comment-id="' + commentId + '"]');
            if (replyForm) {
                replyForm.style.display = 'block'; // Show the reply comment form
            }
        });
    });

    // Event listener for "Cancel" button click
    document.querySelectorAll('.cancel_comment_btn').forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            this.closest('.write_comment').style.display = 'none'; // Hide the comment form
        });
    });

    // Event listener for comment form submission
    document.querySelectorAll('.write_comment form').forEach(function(form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            
            const formData = new FormData(this);
            fetch('submit_comment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert(data); // Display success message or handle response
                window.location.reload();
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
</script>
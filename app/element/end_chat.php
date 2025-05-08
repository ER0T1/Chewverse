<style>
    .rating-container {
        gap: 8px; /* 星星之間的間距 */
    }
    
    .rating-container img {
        width: 40px;
        height: 40px;
        cursor: pointer;
        transition: transform 0.2s;
    }
    
    .rating-container img:hover {
        transform: scale(1.2); /* 滑鼠懸停時星星放大效果 */
    }
    
    .error-message {
        color: #dc3545;
        font-size: 0.85rem;
        text-align: center;
        margin-top: 0.5rem;
        display: none;
    }
</style>

<!-- 確認離開 模態框 -->
<div class="modal fade" id="confirmLeaveModal" tabindex="-1" role="dialog" aria-labelledby="confirmLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmLeaveModalLabel">Confirm Leave</h5>
            </div>

            <div class="modal-body">
                <p>Ready to leave the chat?</p>
                <p>Leaving now will end the conversation.</p>
                <p>Are you sure you want to leave?</p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-gradient" data-bs-dismiss="modal"><span>Cancel</span></button>
                <button type="button" class="btn btn-gradient" id="confirmLeaveBtn"><span>Leave</span></button>
            </div>
        </div>
    </div>
</div>

<!-- 評分 模態框 -->
<div class="modal fade" id="ratingModal" tabindex="-1" role="dialog" aria-labelledby="ratingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ratingModalLabel">Did you enjoy the chat?<br />Give it a quick rating!</h5>
            </div>

            <div class="modal-body">
                <p>Give your chat buddy a rating!</p>
                <div class="rating-container d-flex justify-content-center mb-3" id="ratingStars">
                    <img src="assets/images/star1.png" data-value="1"></img>
                    <img src="assets/images/star1.png" data-value="2"></img>
                    <img src="assets/images/star1.png" data-value="3"></img>
                    <img src="assets/images/star1.png" data-value="4"></img>
                    <img src="assets/images/star1.png" data-value="5"></img>
                </div>
                <p>Can you tell us more?</p>
                <textarea class="form-control" id="ratingComment" rows="3" placeholder="Your feedback is valuable to us!"></textarea>
                <input type="hidden" id="userRating" value="">
                <p class="error-message" id="ratingError">Please select a rating.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-gradient" onclick="window.location.href = 'dashboard.php';"><span>Cancel</span></button>
                <button type="button" class="btn btn-gradient" id="submitRatingBtn"><span>Submit</span></button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
    // 確認離開模態框
    document.querySelector('.end-btn').addEventListener('click', function() {
        var modal = new bootstrap.Modal(document.getElementById('confirmLeaveModal'));
        modal.show();
    });

    document.getElementById('confirmLeaveBtn').addEventListener('click', function() {
        // 關閉第一個模態框
        var comfirmLeaveModal = bootstrap.Modal.getInstance(document.getElementById('confirmLeaveModal'));
        comfirmLeaveModal.hide();

        // 顯示第二個模態框(評分模態框)
        var ratingModal = new bootstrap.Modal(document.getElementById('ratingModal'));
        ratingModal.show();
        // window.location.href = 'dashboard.php'; // 導向首頁或指定頁面
    });

    // 星級評分功能
    let selectedRating = 0;
    const ratingStars = document.querySelectorAll('#ratingStars img');

    // 處理星星懸停效果
    ratingStars.forEach(star => {
        // 滑鼠進入星星時
        star.addEventListener('mouseenter', function() {
            const value = parseInt(this.getAttribute('data-value'));
            highlightStars(value);
        });

        // 滑鼠點擊星星時
        star.addEventListener('click', function() {
            const value = parseInt(this.getAttribute('data-value'));
            selectedRating = value;
            document.getElementById('userRating').value = value;
            document.getElementById('ratingError').style.display = 'none';
            highlightStars(value);
        });
    });

    // 滑鼠離開評分容器時
    document.getElementById('ratingStars').addEventListener('mouseleave', function() {
        // 如果已選擇評分，則保持該評分的星星亮起
        // 否則恢復所有星星為未選中狀態
        if (selectedRating > 0) {
            highlightStars(selectedRating);
        } else {
            resetStars();
        }
    });

    // 高亮顯示星星
    function highlightStars(count) {
        ratingStars.forEach(star => {
            const starValue = parseInt(star.getAttribute('data-value'));
            if (starValue <= count) {
                star.src = 'assets/images/star2.png'; // 高亮星星圖片
            } else {
                star.src = 'assets/images/star1.png'; // 未選中星星圖片
            }
        });
    }

    // 重置所有星星為未選中狀態
    function resetStars() {
        ratingStars.forEach(star => {
            star.src = 'assets/images/star1.png';
        });
    }

    // 提交評分按鈕點擊事件
    document.getElementById('submitRatingBtn').addEventListener('click', function() {
        if (selectedRating === 0) {
            // 顯示錯誤訊息
            document.getElementById('ratingError').style.display = 'block';
            return;
        }

        // 獲取評論內容
        const comment = document.getElementById('ratingComment').value;

        // 這裡可以添加將評分和評論提交到服務器的代碼
        // console.log('Rating:', selectedRating, 'Comment:', comment);

        // 完成評分後跳轉
        window.location.href = 'dashboard.php';
    });
</script>
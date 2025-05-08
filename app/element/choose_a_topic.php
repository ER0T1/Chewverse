<!-- CSS -->
<style>
    .topic-container {
        display: flex;
        flex-direction: column;
        height: 80vh;
        /* 調整高度 */
        margin: 0 auto;
        /* 水平置中 */
    }

    .topic-header {
        padding: 15px;
        /* background-color: #fff; */
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .topic-list {
        flex: 1;
        overflow-y: auto;
        /* 啟用垂直滾動 */
        padding: 10px;
        margin-bottom: 10px;
    }

    .topic-footer {
        padding: 15px;
        position: sticky;
        bottom: 0;
        z-index: 100;
    }

    .topic-btn.active {
        background: #E1894B;
        -webkit-text-fill-color: #E1894B;
        border: 3px solid #E1894B;
        color: #000;
    }
</style>

<?php
$topicFile = 'database/topic.json';
$topics = [];

// 讀取 topic.json 資料
if (file_exists($topicFile)) {
    $topicData = json_decode(file_get_contents($topicFile), true);

    if (json_last_error() === JSON_ERROR_NONE && isset($topicData['topics']) && is_array($topicData['topics'])) {
        $topics = $topicData['topics'];
    } else {
        echo '<p>Error parsing topics data.</p>';
    }
} else {
    echo '<p>Error loading topics.</p>';
}
?>

<div class="topic-container">
    <div class="topic-header">
        <h4 class="text-center mb-0">Choose a topic</h4>
    </div>

    <div class="topic-list">
        <?php foreach ($topics as $topic) : ?>
            <button class="btn btn-outline-general w-100 my-1 topic-btn"><span><?php echo htmlspecialchars($topic, ENT_QUOTES, 'UTF-8') ?></span></button>
        <?php endforeach; ?>
    </div>

    <div class="topic-footer">
        <div class="d-flex justify-content-center">
            <button class="btn btn-gradient" id="start-chat"><span>Start Chat</span></button>
        </div>
    </div>
</div>

<!-- 主題選擇 JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 選擇所有主題按鈕
        const topicButtons = document.querySelectorAll('.topic-btn');
        let selectedTopic = null;

        // 為每個按鈕添加點擊事件
        topicButtons.forEach(button => {
            button.addEventListener('click', function() {
                // 移除所有按鈕的選中狀態
                topicButtons.forEach(btn => {
                    btn.classList.remove('active');
                });

                // 為當前按鈕添加選中狀態
                this.classList.add('active');

                // 保存選中的主題
                selectedTopic = this.querySelector('span').textContent;
            });
        });

        // 處理開始聊天按鈕點擊
        const startChatButton = document.getElementById('start-chat');
        if (startChatButton) {
            startChatButton.addEventListener('click', function() {
                if (!selectedTopic) {
                    alert('Please select a Topic first!');
                    return;
                }

                // 創建並觸發自定義事件，包含選擇的主題
                const topicSelectedEvent = new CustomEvent('topicSelected', {
                    detail: {
                        topic: selectedTopic
                    },
                    bubbles: true,
                    cancelable: true
                });

                // 觸發事件
                startChatButton.dispatchEvent(topicSelectedEvent);
            });
        }
    });
</script>
// Инициализируем объект для хранения данных текущего пользователя
let currentUser = {};

// Переменная для хранения ID текущего друга в чате
let currentChatFriendId = null;

// Асинхронная функция для регистрации пользователя
async function register() {
    // Получаем значения из полей формы регистрации
    const username = document.getElementById('register-username').value;
    const email = document.getElementById('register-email').value;
    const password = document.getElementById('register-password').value;

    // Отправляем запрос на сервер для регистрации
    const response = await fetch('/api/user/register.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, email, password }) // Отправляем данные в формате JSON
    });

    // Получаем ответ от сервера в формате JSON
    const data = await response.json();
    // Выводим сообщение от сервера
    alert(data.message);
}

// Асинхронная функция для входа пользователя
async function login() {
    // Получаем значения из полей формы входа
    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;

    // Отправляем запрос на сервер для входа
    const response = await fetch('/api/user/login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password }) // Отправляем данные в формате JSON
    });

    // Получаем ответ от сервера в формате JSON
    const data = await response.json();
    if (data.user) {
        // Если пользователь найден, сохраняем его данные
        currentUser = data.user;
        // Скрываем блок авторизации и показываем блок профиля и другие разделы
        document.getElementById('auth').style.display = 'none';
        document.getElementById('profile').style.display = 'block';
        document.getElementById('friends').style.display = 'block';
        document.getElementById('posts').style.display = 'block';
        document.getElementById('messages').style.display = 'block';
        // Отображаем информацию о пользователе
        document.getElementById('user-profile').innerHTML = `Добро пожаловать, ${currentUser.username} (ID: ${currentUser.id})`;
        // Загружаем список друзей, постов и чатов
        loadFriends();
        loadPosts();
        loadChats();
    } else {
        // Если ошибка, выводим сообщение от сервера
        alert(data.message);
    }
}

// Асинхронная функция для добавления друга
async function addFriend() {
    // Получаем значение из поля формы добавления друга
    const friendId = document.getElementById('friend-id').value;

    // Отправляем запрос на сервер для добавления друга
    const response = await fetch('/api/friends/add.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: currentUser.id, friend_id: friendId }) // Отправляем данные в формате JSON
    });

    // Получаем ответ от сервера в формате JSON
    const data = await response.json();
    // Выводим сообщение от сервера
    alert(data.message);
    // Обновляем список друзей
    loadFriends();
    // Добавляем уведомление о добавлении друга
    addFriendNotification(friendId);
}

// Функция для добавления уведомления о добавлении друга
async function addFriendNotification(friendId) {
    // Получаем элемент для уведомлений о друзьях
    const friendNotifications = document.getElementById('friend-notifications');
    // Создаем новый элемент для уведомления
    const notificationElement = document.createElement('div');
    // Добавляем классы для стиля уведомления
    notificationElement.classList.add('alert', 'alert-success');
    // Устанавливаем текст уведомления
    notificationElement.innerHTML = `Пользователь с ID ${friendId} добавлен в друзья.`;
    // Добавляем уведомление в элемент для уведомлений
    friendNotifications.appendChild(notificationElement);
}

// Асинхронная функция для загрузки списка друзей
async function loadFriends() {
    // Отправляем запрос на сервер для получения списка друзей
    const response = await fetch(`/api/friends/list.php?user_id=${currentUser.id}`);
    // Получаем список друзей в формате JSON
    const friends = await response.json();
    // Очищаем текущий список друзей
    const friendList = document.getElementById('friends-list');
    friendList.innerHTML = '';

    // Для каждого друга создаем элемент и добавляем его в список
    friends.forEach(friend => {
        const friendElement = document.createElement('div');
        friendElement.classList.add('friend');
        friendElement.innerHTML = `<strong>${friend.username}</strong>`;
        friendList.appendChild(friendElement);
    });
}

// Асинхронная функция для создания нового поста
async function createPost() {
    // Получаем значение из поля формы создания поста
    const content = document.getElementById('post-content').value;

    // Отправляем запрос на сервер для создания поста
    const response = await fetch('/api/post/create.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: currentUser.id, content }) // Отправляем данные в формате JSON
    });

    // Получаем ответ от сервера в формате JSON
    const data = await response.json();
    // Выводим сообщение от сервера
    alert(data.message);
    // Обновляем список постов
    loadPosts();
}

// Асинхронная функция для загрузки списка постов
async function loadPosts() {
    // Отправляем запрос на сервер для получения списка постов
    const response = await fetch('/api/post/list.php');
    // Получаем список постов в формате JSON
    const posts = await response.json();
    // Очищаем текущий список постов
    const postList = document.getElementById('post-list');
    postList.innerHTML = '';

    // Для каждого поста создаем элемент и добавляем его в список
    posts.forEach(post => {
        const postElement = document.createElement('div');
        postElement.classList.add('post');
        postElement.innerHTML = `<strong>${post.username}</strong>: ${post.content}
        <div id="comments-${post.id}"></div>
        <textarea id="comment-${post.id}" class="form-control mt-2" placeholder="Напишите комментарий..."></textarea>
        <button class="btn btn-primary mt-2" onclick="addComment(${post.id})">Добавить комментарий</button>`;
        postList.appendChild(postElement);
        // Загружаем комментарии для каждого поста
        loadComments(post.id);
    });
}

// Асинхронная функция для добавления комментария к посту
async function addComment(postId) {
    // Получаем значение из поля формы добавления комментария
    const content = document.getElementById(`comment-${postId}`).value;

    // Отправляем запрос на сервер для добавления комментария
    const response = await fetch('/api/comments/add.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ post_id: postId, user_id: currentUser.id, content }) // Отправляем данные в формате JSON
    });

    // Получаем ответ от сервера в формате JSON
    const data = await response.json();
    // Выводим сообщение от сервера
    alert(data.message);
    // Обновляем список комментариев для поста
    loadComments(postId);
}

// Асинхронная функция для загрузки комментариев к посту
async function loadComments(postId) {
    // Отправляем запрос на сервер для получения списка комментариев
    const response = await fetch(`/api/comments/list.php?post_id=${postId}`);
    // Получаем список комментариев в формате JSON
    const comments = await response.json();
    // Очищаем текущий список комментариев
    const commentList = document.getElementById(`comments-${postId}`);
    commentList.innerHTML = '';

    // Для каждого комментария создаем элемент и добавляем его в список
    comments.forEach(comment => {
        const commentElement = document.createElement('div');
        commentElement.classList.add('comment');
        commentElement.innerHTML = `<strong>${comment.username}</strong>: ${comment.content}`;
        commentList.appendChild(commentElement);
    });
}

// Асинхронная функция для отправки сообщения
async function sendMessage() {
    // Проверяем, выбран ли чат
    if (currentChatFriendId === null) {
        alert("Выберите чат.");
        return;
    }
    // Получаем значение из поля формы отправки сообщения
    const content = document.getElementById('message-content').value;

    // Отправляем запрос на сервер для отправки сообщения
    const response = await fetch('/api/messages/send.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ sender_id: currentUser.id, receiver_id: currentChatFriendId, content }) // Отправляем данные в формате JSON
    });

    // Получаем ответ от сервера в формате JSON
    const data = await response.json();
    // Выводим сообщение от сервера
    alert(data.message);
    // Обновляем список сообщений для текущего чата
    loadMessages(currentChatFriendId);
    // Обновляем список чатов
    loadChats();
}

// Асинхронная функция для загрузки сообщений в чате
async function loadMessages(friendId) {
    // Устанавливаем текущего друга в чате
    currentChatFriendId = friendId;
    // Устанавливаем значение поля с ID друга
    document.getElementById('message-friend-id').value = friendId;
    // Отправляем запрос на сервер для получения списка сообщений
    const response = await fetch(`/api/messages/list.php?user_id=${currentUser.id}&friend_id=${friendId}`);
    // Получаем список сообщений в формате JSON
    const messages = await response.json();
    // Очищаем текущий список сообщений
    const messageList = document.getElementById('message-list');
    messageList.innerHTML = '';

    // Для каждого сообщения создаем элемент и добавляем его в список
    messages.forEach(message => {
        const messageElement = document.createElement('div');
        messageElement.classList.add('message');
        messageElement.innerHTML = `<strong>${message.sender_id == currentUser.id ? 'Вы' : 'Друг'}:</strong> ${message.content}`;
        messageList.appendChild(messageElement);
    });
}

// Асинхронная функция для загрузки списка чатов
async function loadChats() {
    // Отправляем запрос на сервер для получения списка чатов
    const response = await fetch(`/api/chats/list.php?user_id=${currentUser.id}`);
    // Получаем список чатов в формате JSON
    const chats = await response.json();
    // Очищаем текущий список чатов
    const chatList = document.getElementById('chat-list');
    chatList.innerHTML = '';

    // Для каждого чата создаем элемент и добавляем его в список
    chats.forEach(chat => {
        const chatElement = document.createElement('div');
        chatElement.classList.add('chat');
        chatElement.setAttribute('friend-id', chat.id);
        chatElement.innerHTML = `<strong>${chat.username}</strong>`;
        // Добавляем обработчик клика для загрузки сообщений при выборе чата
        chatElement.addEventListener('click', () => {
            loadMessages(chat.id);
        });
        chatList.appendChild(chatElement);
    });
}

// Require the packages we will use:
var http = require("http"),
	socketio = require("socket.io"),
	fs = require("fs");

// Listen for HTTP connections.  This is essentially a miniature static file server that only serves our one file, client.html:
var app = http.createServer(function(req, resp){
	// This callback runs when a new connection is made to our HTTP server.
	
	fs.readFile("client_savable.html", function(err, data){
		// This callback runs when the client.html file has been read from the filesystem.
		
		if(err) return resp.writeHead(500);
		resp.writeHead(200);
		resp.end(data);
	});
});
app.listen(3456);

let rooms_arr = [];
let users_arr = [];
let user_room_dict = {};
let creator_room_dict = {};
let user_socket_id_dict = {};
let temp_ban_room_dict = {};
let perm_ban_room_dict = {};
let private_room_dict = {};


// Do the Socket.IO magic:
var io = socketio.listen(app);
io.sockets.on("connection", function(socket){
	// This callback runs when a new Socket.IO connection is established.
	
	socket.on('message_to_server', function(data) {

		console.log("message: "+data["message"]);
		// alert("username: " +data["user_name"]); // log it to the Node.JS output
		
		io.sockets.emit("message_to_client",{message:data["message"], chatroom: data["chatroom"], user: data["user"]}); // broadcast the message to other users
	});
	
	socket.on('emoji_to_server', function(data){
		
		let emote = data["emote"]
		io.sockets.emit("emoji_to_client", {emoji: emote, chatroom:data["chatroom"], user:data["user"]})
		})
	
	socket.on('chatroom_to_server', function(data) {
		// This callback runs when the server receives a new message from the client.
		let chatroom_name = data["chatroom"]
		let password = data["pass"]
		rooms_arr.push(chatroom_name);
		creator_room_dict[chatroom_name] = data["user"];
		if(password != ""){
			private_room_dict[chatroom_name] = password;
		}
		let protectstat = String(password!="");
		//user_room_dict[data["chatroom"]] = ",";
		console.log("chatroom: "+chatroom_name); // log it to the Node.JS output
		console.log("created by: " + creator_room_dict[chatroom_name])
		io.sockets.emit("chatroom_to_client",{chatroom:chatroom_name, hasPass: protectstat}); // broadcast the message to other users
	});
	
	socket.on("request_chatrooms", function(data){
		//make comma delimited list of chatrooms
		console.log("called pls");
		let chatroom_list = "";
		let private_list = ""
		for(let i = 0; i<rooms_arr.length; i++){
			chatroom_list += rooms_arr[i] + ",";
			console.log("chatroomlist");
			console.log(chatroom_list);
			if(rooms_arr[i] in private_room_dict){
				private_list += rooms_arr[i] + ","
			}
		}
		io.sockets.emit("sent_chatrooms", {chatroomlist:chatroom_list, privatelist: private_list});//broadcast to users
		});
	
	socket.on("user_to_server", function(data) {
		let possible_username = data["user"];
		let user_exists = false;
		for (let i = 0; i < users_arr.length; i++) {
			if (possible_username == users_arr[i]){
				user_exists = true;
				}
		}
		console.log("existence")
		console.log(user_exists)
		if (!user_exists) {
			users_arr.push(possible_username);
			user_socket_id_dict[possible_username] = socket.id;
			console.log(user_socket_id_dict);
		}
		console.log("stored user!");
		console.log("USER ARRAY!!!!!!!!")
		console.log(users_arr)
		console.log(user_exists)
		io.sockets.emit("user_exists_check",{user: possible_username, userExists: String(user_exists)});
	});
	
	socket.on("user_to_chatroom_to_server", function(data){//gets chatroom, user
		let user_chatroom = data["chatroom"];
		let current_user = data["user"];
		let isBanned;
		if(perm_ban_room_dict[user_chatroom]!=null){
			isBanned = current_user in perm_ban_room_dict[user_chatroom];
			console.log(current_user in perm_ban_room_dict[user_chatroom]);
			for(let i = 0; i<perm_ban_room_dict[user_chatroom].length; i++){
				if(current_user==(perm_ban_room_dict[user_chatroom])[i]){
					isBanned = true;
				}
			}
			console.log(isBanned);
		}
		else{
			isBanned = false;
		}
		console.log("BAN STATUS FOR "+ current_user + " IS: " + String(isBanned));
		
		console.log(perm_ban_room_dict[user_chatroom])
		if(!isBanned){
			console.log(current_user + " " + user_chatroom);
			let bool_boi = false;
			
			if(!(user_chatroom in user_room_dict)){
				user_room_dict[user_chatroom] = []
			}
			
			let chatroom_user_arr = user_room_dict[user_chatroom]
			for (let i = 0; i < chatroom_user_arr.length; i++) {
				if (chatroom_user_arr[i] == current_user) {
					bool_boi = true;
				}
			}
			
			if (!bool_boi) {
				user_room_dict[user_chatroom].push(current_user);
			}
	
			
			let chatroom_user_list = "";
			
			for(let i = 0; i<chatroom_user_arr.length; i++){
				chatroom_user_list += chatroom_user_arr[i] + ",";
			}
			let bool = String(data["user"]==creator_room_dict[user_chatroom]);
			io.sockets.emit("user_to_chatroom_to_client", {chatroom_users: chatroom_user_list, chatroom:user_chatroom, isCreator: bool, creator: creator_room_dict[user_chatroom]});
		}
		else{
			io.sockets.emit("reset_user_to_main_page", {banned_user: current_user})
		}
		
	});


	socket.on("remove_user_from_chatroom", function(data) {
		//let new_user_arr = [];
		const room_membership = data["kick_ban_cond"];
		const user_chatroom = data["chatroom"]
		const cur_user = data["user"];
		let old_arr = user_room_dict[user_chatroom];
		let user_list = "";
		
		if (room_membership == "ban") {
			if(perm_ban_room_dict[user_chatroom] == null){
				perm_ban_room_dict[user_chatroom] = [];
			}
			perm_ban_room_dict[user_chatroom].push(cur_user)
		}
		if (room_membership != "") {
			socket.broadcast.to(user_socket_id_dict[cur_user]).emit("reset_user_to_main_page", {banned_user: ""})
		}	
		
		
		console.log("removing from chatroom: " + String(user_chatroom));
		let index;
		for (let i = 0; i < old_arr.length; i++) {
			if (cur_user == old_arr[i]) {
				index = i;
			}
		}
		user_room_dict[user_chatroom].splice(index,1);
		
		for(let i = 0; i<user_room_dict[user_chatroom].length; i++){
			user_list += (user_room_dict[user_chatroom])[i] + ",";
		}
		
		//user_room_dict[user_chatroom] = new_user_arr;
		console.log("USERS IN THIS CHATROOM AFTER REMOVAL ARE: ")
		console.log(user_room_dict[user_chatroom])
		io.sockets.emit("receive_updated_chatroom_users", {chatroom_users: user_list, chatroom: user_chatroom, creator: creator_room_dict[user_chatroom]});
	});
	socket.on("dm_to_server", function(data){
		let recipient = data["recip"];
		let message = data["msg"];
		let user = data["user"]
		
		socket.broadcast.to(user_socket_id_dict[recipient]).emit("dm_to_client",{msg: message, user:user});
		});
	socket.on("check_password", function(data){
		let chatroom_name = data["chatroom"]
		let password = data["pass"]
		let username = data["user"]
		console.log("CHECKING PASSWORD!!!!!!!")
		console.log("stored password: ")
		console.log(private_room_dict[chatroom_name])
		console.log("given password:")
		console.log(password)
		if(password == private_room_dict[chatroom_name]){
			console.log(user_socket_id_dict[username])
			io.sockets.emit("post_password_check_client", {alloweduser: username, passed: "true"})
		}
		else{
			io.sockets.emit("post_password_check_client", {alloweduser: username, passed: "false"})
		}
		
		});
	
	
});
"use strict";

// Copyright By Ilman Sunanuddin, M pedia
// Email : Ilmansunannudin2@gmail.com
// website : https://m-pedia.co.id
// Whatsap : 6282298859671
// ------------------------------------------------------------------
// You are not allowed to share or sell this source code without permission.

const wa = require("./server/whatsapp");

const fs = require("fs");
require("dotenv").config();
const lib = require("./server/lib");
global.log = lib.log;

/**
 * EXPRESS FOR ROUTING
 */
const express = require("express");
const app = express();
const http = require("http");
const server = http.createServer(app);

/**
 * SOCKET.IO
 */
const { Server } = require("socket.io");
const io = new Server(server);
const port = process.env.PORT_NODE;
app.use((req, res, next) => {
  res.set("Cache-Control", "no-store");
  req.io = io;
  // res.set('Cache-Control', 'no-store')
  next();
});

const bodyParser = require("body-parser");

// parse application/x-www-form-urlencoded
app.use(
  bodyParser.urlencoded({
    extended: false,
    limit: "50mb",
    parameterLimit: 100000,
  })
);
// parse application/json
app.use(bodyParser.json());
app.use(express.static("src/public"));
app.use(require("./server/router"));

// console.log(process.argv)

io.on("connection", (socket) => {
  socket.on("StartConnection", (data) => {
    wa.connectToWhatsApp(data, io);
  });
  socket.on("ConnectViaCode", (data) => {
    wa.connectToWhatsApp(data, io, true);
  });
  socket.on("LogoutDevice", (device) => {
    wa.deleteCredentials(device, io);
  });
});
server.listen(port, console.log(`Server run and listening port: ${port}`));

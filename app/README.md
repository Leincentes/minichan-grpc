## End-to-End Chat Application using minichan-v1

This is an end-to-end chat application using the minichan-v1 library. This application will allow users to send and receive messages in real-time over gRPC.

### Overview

Our chat application will consist of the following components:

1. **Server**: A gRPC server implemented using the minichan-v1 library to handle incoming chat messages from clients.
   
2. **Client**: A gRPC client application that allows users to send and receive messages from the server.
   
3. **Database**: A simple MySQL database to store chat messages.

### Getting Started

To get started, follow these steps:

1. **Database Setup:**
   - Create a MySQL database to store user information using the *chatapp.sql*.

2. **Start the gRPC Server:**
   - Ensure that the gRPC server provided by the Minichan library is running and accessible.

3. **Run the Application:**
   - Execute `php app/App.php` to start the application.

### Blog

Blog link: **https://medium.com/@leincano/building-an-end-to-end-chat-application-with-minichan-v1-39510b388bfe**

### Demonstration

Demo link: **http://ec2-18-183-233-22.ap-northeast-1.compute.amazonaws.com/**

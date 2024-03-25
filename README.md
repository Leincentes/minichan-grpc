# minichan-v1

<img src="/assets/logo.svg" alt="Project Logo" width="200" height="200">

**minichan-v1** is a PHP gRPC library that leverages Swoole coroutines. It encompasses a protoc code generator, server, and client components to facilitate the development of gRPC-based applications.

## Overview

minichan-v1 is designed to simplify the development of gRPC-based applications in PHP. It provides a comprehensive set of tools, including a protoc code generator, server, and client components, all powered by Swoole coroutines. With minichan-v1, developers can build efficient and scalable gRPC services with ease.

## Dedicated Documentation

For detailed documentation and usage examples, please refer to our dedicated documentation site:

[**minichan Documentation**](https://minichan-docs.vercel.app/#/?id=minichan-v1)

Explore the documentation to learn how to get started, generate PHP code via protoc, set up a basic gRPC server and client, and more.

## JMeter Test Cases

We've included JMeter test cases to ensure the performance and reliability of minichan-v1. You can find the test plans in the `jmeter` directory of this repository:

### Overall Test Cases for most methods

#### Test Plan Overview:
![Test Plan Overview](/assets/Overall.png)

#### Statistics Analysis:
![Statistics Analysis](/assets/statistics.png)

## Sample Application

To demonstrate the capabilities of minichan-v1, we've included a sample.

[**Login|Register Form**](https://github.com/Leincentes/minichan-grpc/tree/login-register-sample/app)
<br>

[**End-to-End Chatapp**](https://github.com/Leincentes/minichan-grpc/tree/chatapp-sample/app)

## License

This project is licensed under the [MIT License](LICENSE).

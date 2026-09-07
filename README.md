# Native PHP Framework Lab [French version](README_fr.md)

An experimental and educational project to explore **native PHP development** and understand how a web framework works internally.

> ⚠️ This project is intended for learning and experimentation. It is **not designed for production use**.

## 🎯 Project purpose

Modern PHP frameworks such as Symfony, Laravel or Yii provide many powerful abstractions that make web development easier.

But using these abstractions can sometimes hide the mechanisms behind them.

The purpose of this project is to rebuild some of these mechanisms from scratch using mostly **native PHP**, in order to better understand how they work.

The goal is not to create another production-ready PHP framework.

The goal is to **experiment, learn, understand and improve**.

## 🔬 What is being explored?

The project currently experiments with concepts such as:

* Application lifecycle
* HTTP Request and Response
* Routing
* URL parsing and generation
* Controllers and actions
* Views and layouts
* HTML helpers
* Asset management
* Tailwind CSS integration
* PDO and database connections
* SQL Query Builder
* Prepared statements and parameter binding
* Models and validation
* Simplified Active Record implementation
* Model queries
* Query providers
* Pagination

More experiments will be added progressively.

Possible future topics include:

* API endpoints and JSON responses
* Angular integration
* Authentication and authorization
* Sessions and cookies
* Middleware
* Dependency injection
* Events
* Caching
* Logging
* Error handling
* Automated tests

## 🧱 General architecture

The project progressively explores a request lifecycle similar to:

```text
HTTP Request
     ↓
Application
     ↓
Request
     ↓
Router
     ↓
Controller
     ↓
Model / Query / PDO
     ↓
Controller
     ↓
View
     ↓
Response
     ↓
HTTP Response
```

The objective is to understand the responsibility of each layer rather than hiding the implementation behind a large abstraction.

## 🗄️ Database experimentation

The database layer is based on **PDO** and experiments with several concepts commonly found in frameworks:

```text
Connection
    ↓
Query
    ↓
PdoModelQuery
    ↓
PdoModel
```

Queries use prepared statements and keep SQL structure separate from parameter values.

A simplified Active Record approach is also being explored.

## 🎨 Views and assets

The project also explores the frontend side of a PHP framework:

```text
View / Layout
      ↓
AssetBundle
      ↓
AssetManager
      ↓
CSS / JavaScript
      ↓
Browser
```

Tailwind CSS is currently used to experiment with asset compilation, publication and integration into PHP views.

## 🧪 A laboratory, not a finished framework

Some implementations may intentionally remain simple.

Others may be rewritten several times as new concepts are explored.

That's part of the project.

The repository is intended to document the process of understanding **why frameworks are designed the way they are**, not simply to reproduce an existing framework.

Ideas from established frameworks may naturally inspire some experiments, but the objective is to understand and implement the underlying concepts independently.

## 🤝 Contributions are welcome

**Everyone is welcome to participate.**

Suggestions, discussions, code reviews, alternative implementations and pull requests are welcome.

If you see:

* a design that could be improved,
* a PHP mechanism worth exploring,
* a security issue,
* an interesting architectural alternative,
* a bug,
* or simply another way of implementing something,

feel free to open an **Issue** or propose a **Pull Request**.

The objective is not to find the only correct implementation, but to learn from different approaches.

## 💡 Philosophy

The main idea behind this repository is simple:

> **Understanding what a framework does by trying to build the mechanisms ourselves.**

Sometimes the best way to understand an abstraction is to remove it and see what is happening underneath.

## ⚠️ Disclaimer

This is an educational and experimental project.

It may contain incomplete implementations, breaking changes or approaches that would require additional security, testing and optimization before being considered for production use.

**Do not use it as a production framework.**

## 📜 License

A license will be defined for the project.

If you would like to contribute before then, feel free to open an issue to discuss it.
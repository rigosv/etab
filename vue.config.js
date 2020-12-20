const path = require("path");

module.exports = {
  chainWebpack: config => {
    config
      .entry("app")
      .clear()
      .add("./assets/main.ts")
      .end();
    config.resolve.alias
      .set("@", path.join(__dirname, "./assets"));
  }
};
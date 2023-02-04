import SettingsPublicKey from "./components/SettingsPublicKey";
import { extend } from "flarum/common/extend";
import app from "flarum/forum/app";
import SettingsPage from "flarum/forum/components/SettingsPage";

app.initializers.add("nearata-encrypt-mail", () => {
  extend(SettingsPage.prototype, "settingsItems", function (items) {
    items.add("nearataEncryptMailPublicKey", SettingsPublicKey.component());
  });
});

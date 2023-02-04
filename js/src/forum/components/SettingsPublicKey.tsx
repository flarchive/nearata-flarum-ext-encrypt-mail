import Component from "flarum/common/Component";
import Button from "flarum/common/components/Button";
import FieldSet from "flarum/common/components/FieldSet";
import Stream from "flarum/common/utils/Stream";
import app from "flarum/forum/app";
import type Mithril from "mithril";

export default class SettingsPublicKey extends Component {
  loading!: boolean;
  content!: Stream<string>;

  oninit(vnode: Mithril.Vnode<this>): void {
    super.oninit(vnode);

    this.loading = false;
    this.content = Stream(
      app.session.user!.attribute("nearataEncryptMailPublicKey")
    );
  }

  oncreate(vnode: Mithril.VnodeDOM<this>): void {
    super.oncreate(vnode);
  }

  view(vnode: Mithril.Vnode<this>) {
    const label = app.translator.trans(
      "nearata-encrypt-mail.forum.settings.field_label"
    );

    return (
      <FieldSet className="Settings-nearataEncryptMailPublicKey" label={label}>
        <textarea
          class="FormControl"
          rows="10"
          autocomplete="off"
          bidi={this.content}
        ></textarea>
        <Button
          className="Button Button--primary"
          type="submit"
          onclick={this.onSubmit.bind(this)}
          loading={this.loading}
        >
          {app.translator.trans(
            "nearata-encrypt-mail.forum.settings.save_changes"
          )}
        </Button>
      </FieldSet>
    );
  }

  onSubmit(event: PointerEvent): void {
    const oldContent = app.session.user!.attribute(
      "nearataEncryptMailPublicKey"
    );

    if (this.content() === oldContent) {
      return;
    }

    this.loading = true;

    app
      .request({
        url:
          app.forum.attribute("apiUrl") +
          "/nearata/encryptMail/updatePublicKey",
        method: "POST",
        body: { publicKey: this.content() },
      })
      .then(() => location.reload())
      .catch(() => {
        this.loading = false;
        m.redraw();
      });
  }
}

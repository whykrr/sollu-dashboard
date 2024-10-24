const Ziggy = {"url":"http:\/\/sollu-dashboard.test","port":null,"defaults":{},"routes":{"debugbar.openhandler":{"uri":"_debugbar\/open","methods":["GET","HEAD"]},"debugbar.clockwork":{"uri":"_debugbar\/clockwork\/{id}","methods":["GET","HEAD"],"parameters":["id"]},"debugbar.assets.css":{"uri":"_debugbar\/assets\/stylesheets","methods":["GET","HEAD"]},"debugbar.assets.js":{"uri":"_debugbar\/assets\/javascript","methods":["GET","HEAD"]},"debugbar.cache.delete":{"uri":"_debugbar\/cache\/{key}\/{tags?}","methods":["DELETE"],"parameters":["key","tags"]},"debugbar.queries.explain":{"uri":"_debugbar\/queries\/explain","methods":["POST"]},"dashboard":{"uri":"\/","methods":["GET","HEAD"]},"colors":{"uri":"colors","methods":["GET","HEAD"]},"typography":{"uri":"typography","methods":["GET","HEAD"]},"base.cards":{"uri":"base\/cards","methods":["GET","HEAD"]},"base.navigation":{"uri":"base\/navigation","methods":["GET","HEAD"]},"base.placeholders":{"uri":"base\/placeholders","methods":["GET","HEAD"]},"base.spinners":{"uri":"base\/spinners","methods":["GET","HEAD"]},"base.tables":{"uri":"base\/tables","methods":["GET","HEAD"]},"buttons.buttons":{"uri":"buttons\/buttons","methods":["GET","HEAD"]},"buttons.button-group":{"uri":"buttons\/button-group","methods":["GET","HEAD"]},"charts":{"uri":"charts","methods":["GET","HEAD"]},"forms.form-control":{"uri":"forms\/form-control","methods":["GET","HEAD"]},"forms.select":{"uri":"forms\/select","methods":["GET","HEAD"]},"forms.radio":{"uri":"forms\/radio","methods":["GET","HEAD"]},"forms.check":{"uri":"forms\/check","methods":["GET","HEAD"]},"forms.input-group":{"uri":"forms\/input-group","methods":["GET","HEAD"]},"forms.floating-label":{"uri":"forms\/floating-label","methods":["GET","HEAD"]},"forms.layout":{"uri":"forms\/layout","methods":["GET","HEAD"]},"forms.validation":{"uri":"forms\/validation","methods":["GET","HEAD"]},"notification.alert":{"uri":"notification\/alert","methods":["GET","HEAD"]},"notification.modal":{"uri":"notification\/modal","methods":["GET","HEAD"]},"notification.badge":{"uri":"notification\/badge","methods":["GET","HEAD"]},"notification.toast":{"uri":"notification\/toast","methods":["GET","HEAD"]},"widgets":{"uri":"widgets","methods":["GET","HEAD"]},"pages.login":{"uri":"pages\/login","methods":["GET","HEAD"]},"pages.register":{"uri":"pages\/register","methods":["GET","HEAD"]},"error-pages.error-404":{"uri":"error-pages\/error-404","methods":["GET","HEAD"]},"error-pages.error-500":{"uri":"error-pages\/error-500","methods":["GET","HEAD"]},"storage.local":{"uri":"storage\/{path}","methods":["GET","HEAD"],"wheres":{"path":".*"},"parameters":["path"]}}};
if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
  Object.assign(Ziggy.routes, window.Ziggy.routes);
}
export { Ziggy };

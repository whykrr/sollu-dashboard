import { createApp, defineComponent, h } from 'vue';
import { renderToString } from '@vue/server-renderer';

let triggered = false;

const Child = defineComponent({
  emits: ['custom-event'],
  template: '<div>Child</div>',
  mounted() {
    this.$emit('custom-event');
  }
});

const Parent = defineComponent({
  components: { Child },
  template: '<Child v-on="events" />',
  data() {
    return {
      events: {
        'custom-event': () => { triggered = true; }
      }
    };
  }
});

const app = createApp(Parent);
app.mount(document.createElement('div'));
console.log('Triggered:', triggered);

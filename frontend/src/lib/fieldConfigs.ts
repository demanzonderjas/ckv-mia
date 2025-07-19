// Field configs for each block type
export const blockFieldConfigs = {
  image: [
    { name: 'type', label: 'Type', type: 'select', required: true, options: [
      { label: 'Image', value: 'image' },
      { label: 'Rich', value: 'rich' },
    ]},
    { name: 'title', label: 'Titel', type: 'text', required: true },
    { name: 'content', label: 'Image URL', type: 'text', required: true, placeholder: 'Afbeelding URL' },
  ],
  rich: [
    { name: 'type', label: 'Type', type: 'select', required: true, options: [
      { label: 'Image', value: 'image' },
      { label: 'Rich', value: 'rich' },
    ]},
    { name: 'title', label: 'Titel', type: 'text', required: true },
    { name: 'content', label: 'Rich Content', type: 'rich', required: true },
  ]
};

// Parse content for each block type
export function parseBlockContent(type: string, content: any): string {
  try {
    const parsed = typeof content === 'string' ? JSON.parse(content) : content;
    if (type === 'text') return parsed?.text || '';
    if (type === 'image') return parsed?.url || '';
    if (type === 'rich') return parsed?.html || '';
    return '';
  } catch {
    return '';
  }
} 